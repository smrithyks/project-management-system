<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

#[\AllowDynamicProperties]

class Reports extends CI_Controller {
    private $secretKey = '123456';

    public function __construct() {
        parent::__construct();
        $this->load->model('Report_model');
        $this->load->helper('jwt');
    }

    public function projectsummary($projectId) {
        $userId = $this->authorize();
        if ($userId) {
            $report = $this->Report_model->get_project_report($projectId);
            echo json_encode(['status' => true, 'report' => $report]);
        }
    }

    private function authorize() {
        $headers = $this->input->request_headers();
        if (isset($headers['Authorization'])) {
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $decoded = validateJWT($token, $this->secretKey);
            if ($decoded && isset($decoded->data->id)) {
                return $decoded->data->id; 
            }
        }
        echo json_encode(['status' => false, 'message' => 'Unauthorized']);
        return null;
    }

    public function download_report($projectId) {
        $userId = $this->authorize();
        if (!$userId) return;

        $report = $this->Report_model->get_project_report($projectId);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Task ID');
        $sheet->setCellValue('B1', 'Title');
        $sheet->setCellValue('C1', 'Description');
        $sheet->setCellValue('D1', 'Status');
        $sheet->setCellValue('E1', 'Created At');
        $sheet->setCellValue('F1', 'Remarks');

        $row = 2;
        foreach ($report as $task) {
            $remarksText = '';
            foreach ($task->remarks as $remark) {
                $remarksText .= $remark->remark . "\n";
            }

            $sheet->setCellValue("A{$row}", $task->task_id);
            $sheet->setCellValue("B{$row}", $task->title);
            $sheet->setCellValue("C{$row}", $task->description);
            $sheet->setCellValue("D{$row}", $task->status);
            $sheet->setCellValue("E{$row}", $task->created_at);
            $sheet->setCellValue("F{$row}", $remarksText);

            $row++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="project_report.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function status_history($projectId) {
        $userId = $this->authorize();
        if ($userId) {
            $report = $this->Report_model->get_status_history_report($projectId);
            echo json_encode(['status' => true, 'report' => $report]);
        }
    }

    public function statushistorydownload($projectId) {
        $userId = $this->authorize();
        if (!$userId) return;

        $report = $this->Report_model->get_status_history_report($projectId);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header row
        $sheet->setCellValue('A1', 'Sl No');
        $sheet->setCellValue('B1', 'Project');
        $sheet->setCellValue('C1', 'Title');
        $sheet->setCellValue('D1', 'Status');
        $sheet->setCellValue('E1', 'Changed At');

        $row = 2;
        $slNo = 1;
        foreach ($report as $entry) {
            $sheet->setCellValue("A{$row}", $slNo);
            $sheet->setCellValue("B{$row}", $entry->name);
            $sheet->setCellValue("C{$row}", $entry->title);
            $sheet->setCellValue("D{$row}", $entry->status);
            $sheet->setCellValue("E{$row}", $entry->changed_at);
            $row++;
            $slNo++;
        }

        // Excel download headers
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="status_history_report.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function remarks_report($projectId) {
        $userId = $this->authorize();
        if ($userId) {
            $report = $this->Report_model->get_daily_remarks_report($projectId);
            echo json_encode(['status' => true, 'report' => $report]);
        }
    }

    public function dailyremarksdownload($projectId) {
        $userId = $this->authorize();
        if (!$userId) return;

        $report = $this->Report_model->get_daily_remarks_report($projectId);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header row
        $sheet->setCellValue('A1', 'Sl No');
        $sheet->setCellValue('B1', 'Task Title');
        $sheet->setCellValue('C1', 'Project Name');
        $sheet->setCellValue('D1', 'Remark');
        $sheet->setCellValue('E1', 'Created At');

        $row = 2;
        $slNo = 1;
        foreach ($report as $task) {
            $sheet->setCellValue("A{$row}", $slNo);
            $sheet->setCellValue("B{$row}", $task->title);
            $sheet->setCellValue("C{$row}", $task->name);
            $sheet->setCellValue("D{$row}", $task->remark);
            $sheet->setCellValue("E{$row}", $task->created_at);
            $row++;
            $slNo++;
        }

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="daily_remarks_report.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}