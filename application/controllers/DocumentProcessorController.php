<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DocumentProcessorController extends CI_Controller
{
  public function index()
  {
    $this->load->view('upload_form');
  }

  public function upload()
  {
    // Upload config
    $config['upload_path'] = './public/uploads/';
    $config['allowed_types'] = 'doc|docx|rtf|odt|xls|xlsx|pdf';
    $config['encrypt_name'] = TRUE;

    $this->load->library('upload', $config);

    if (!$this->upload->do_upload('template_file')) {
      $data['error'] = $this->upload->display_errors();
      $this->load->view('upload_form', $data);
    } else {
      $uploadData = $this->upload->data();
      $templatePath = $uploadData['full_path'];

      $jsonData = file_get_contents($_FILES['data_file']['tmp_name']);
      $dataArray = parse_json($jsonData);

      $processedFile = $this->documentprocessor->process($templatePath, $dataArray);

      // Send file to user
      $this->load->helper('download');
      force_download($processedFile, NULL);
    }
  }
}
