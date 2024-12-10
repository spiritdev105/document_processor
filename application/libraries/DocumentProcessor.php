<?php
defined('BASEPATH') or exit('No direct script access allowed');

require 'vendor/autoload.php'; // Load PHPWord or PHPSpreadsheet

class DocumentProcessor
{
  public function process($filePath, $dataArray)
  {
    // Determine file type
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);

    switch ($extension) {
      case 'doc':
      case 'docx':
      case 'rtf':
      case 'odt':
        return $this->processWordDocument($filePath, $dataArray);
      case 'xls':
      case 'xlsx':
        return $this->processExcelDocument($filePath, $dataArray);
      case 'pdf':
        return $this->processPDFDocument($filePath, $dataArray);
      default:
        throw new Exception("Unsupported file type: $extension");
    }
  }

  private function processWordDocument($filePath, $dataArray)
  {
    $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
    foreach ($dataArray as $key => $value) {
      $phpWord->setValue("{{{$key}}}", $value);
    }
    $processedFile = './public/uploads/processed_' . basename($filePath);
    $phpWord->save($processedFile);
    return $processedFile;
  }

  private function processExcelDocument($filePath, $dataArray)
  {
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
    // Replace placeholders (Example: loop through cells)
    $processedFile = './public/uploads/processed_' . basename($filePath);
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save($processedFile);
    return $processedFile;
  }

  private function processPDFDocument($filePath, $dataArray)
  {
    // Implement PDF placeholder replacement logic (e.g., using FPDI or TCPDF)
  }
}
