<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpWord\TemplateProcessor;

class DocumentProcessorController extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->helper(['form', 'url', 'download']);
    $this->load->library('upload');
  }

  public function index()
  {
    // Load the view for the upload form
    $this->load->view('upload_form');
  }

  public function upload()
  {
    // Create upload directory if it doesn't exist
    $upload_path = FCPATH . 'public/uploads/';
    if (!is_dir($upload_path)) {
      if (!mkdir($upload_path, 0777, true)) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create upload directory']);
        return;
      }
    }

    // Configure file upload for template file
    $config_template = array(
      'upload_path' => $upload_path,
      'allowed_types' => 'docx',
      'file_ext_tolower' => true,
      'max_size' => '5120', // 5MB max-size
      'overwrite' => true,
      'remove_spaces' => true
    );

    // Upload Template File
    $this->upload->initialize($config_template);
    if (!$this->upload->do_upload('template_file')) {
      echo json_encode(['status' => 'error', 'message' => 'Error uploading template: ' . $this->upload->display_errors('', '')]);
      return;
    }
    $upload_data = $this->upload->data();
    $templateFilePath = $upload_data['full_path'];

    // Check if JSON file exists
    if (!isset($_FILES['data_file']) || !is_uploaded_file($_FILES['data_file']['tmp_name'])) {
      echo json_encode(['status' => 'error', 'message' => 'No JSON file uploaded']);
      return;
    }

    // Read and validate JSON content
    $jsonContent = file_get_contents($_FILES['data_file']['tmp_name']);
    if ($jsonContent === false) {
      echo json_encode(['status' => 'error', 'message' => 'Could not read JSON file']);
      return;
    }

    $jsonData = json_decode($jsonContent);
    if ($jsonData === null) {
      echo json_encode(['status' => 'error', 'message' => 'Invalid JSON file']);
      return;
    }

    // Save JSON file
    $dataFilePath = $upload_path . basename($_FILES['data_file']['name']);
    if (!move_uploaded_file($_FILES['data_file']['tmp_name'], $dataFilePath)) {
      echo json_encode(['status' => 'error', 'message' => 'Error saving JSON file']);
      return;
    }

    // Process the document
    $this->processDocument($templateFilePath, json_decode($jsonContent, true));
  }

  private function processDocument($templateFilePath, $jsonData)
  {
    // Load PHPWord TemplateProcessor
    try {
      $templateProcessor = new TemplateProcessor($templateFilePath);

      // Replace placeholders with JSON data
      foreach ($jsonData as $key => $value) {
        if (is_array($value)) {
          // Handle nested arrays or multidimensional data
          $templateProcessor->cloneRow($key, count($value));
          foreach ($value as $index => $row) {
            foreach ($row as $subKey => $subValue) {
              $templateProcessor->setValue($key . '#' . ($index + 1) . '.' . $subKey, $subValue);
            }
          }
        } else {
          // Replace single placeholders
          $templateProcessor->setValue("{{$key}}", $value);
        }
      }

      // Save the processed document
      $processedFilePath = './uploads/processed_document.docx';
      $templateProcessor->saveAs($processedFilePath);

      // Provide the processed file for download
      echo json_encode(['status' => 'success', 'output' => 'Processed document available for download.']);
      force_download($processedFilePath, null);

    } catch (Exception $e) {
      echo json_encode(['status' => 'error', 'message' => 'Error processing document: ' . $e->getMessage()]);
    }
  }
}
