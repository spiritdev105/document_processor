<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document Processor - Upload Template</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f8f9fa;
    }

    .container {
      margin-top: 50px;
    }

    .card {
      border: none;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .result {
      margin-top: 20px;
      padding: 15px;
      background-color: #f0f8ff;
      border-radius: 8px;
    }

    #error-message {
      color: red;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="card">
      <div class="card-header text-center bg-primary text-white">
        <h3>Upload Document Template and Data</h3>
      </div>
      <div class="card-body">
        <form id="uploadForm" enctype="multipart/form-data">
          <!-- File Input for Template -->
          <div class="form-group">
            <label for="templateFile">Document Template</label>
            <input type="file" id="templateFile" name="template_file" class="form-control"
              accept=".doc,.docx,.rtf,.odt,.xls,.xlsx,.pdf" required>
            <small class="form-text text-muted">Supported formats: .doc, .docx, .rtf, .odt, .xls, .xlsx, .pdf</small>
          </div>

          <!-- File Input for JSON -->
          <div class="form-group">
            <label for="dataFile">Data JSON File</label>
            <input type="file" id="dataFile" name="data_file" class="form-control" accept=".json" required>
            <small class="form-text text-muted">Upload the JSON file containing data for placeholder
              replacement.</small>
          </div>

          <!-- Submit Button -->
          <div class="text-center">
            <button type="button" id="processButton" class="btn btn-primary btn-block">Process Document</button>
          </div>
        </form>

        <!-- Result Area -->
        <div id="result" class="result" style="display:none;">
          <h5>Processed Document Output</h5>
          <pre id="resultOutput"></pre>
        </div>

        <div id="error-message" style="display:none;"></div>
      </div>
    </div>
  </div>

  <!-- Include Bootstrap JS and jQuery -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Handle Form Submission via AJAX
    $('#processButton').on('click', async function (e) {
      e.preventDefault();

      // Get file inputs
      const templateFile = $('#templateFile')[0].files[0];
      const dataFile = $('#dataFile')[0].files[0];

      // Validation: Ensure both files are selected
      if (!templateFile || !dataFile) {
        $('#error-message')
          .text('Both template and JSON files are required.')
          .show();
        return;
      }

      // Prepare FormData
      let formData = new FormData();
      formData.append('template_file', templateFile);
      formData.append('data_file', dataFile);

      // Update Button State
      $('#processButton')
        .prop('disabled', true)
        .html('<span class="spinner-border spinner-border-sm"></span> Processing...');
      $('#error-message').hide();

      // AJAX Request
      $.ajax({
        url: '<?php echo base_url('upload'); ?>',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
          try {
            const result = JSON.parse(response);
            if (result.status === 'success') {
              $('#response')
                .text('Document processed successfully! Downloading...')
                .addClass('success')
                .removeClass('error')
                .show();

              // Trigger File Download
              window.location.href = '<?= base_url('uploads/processed_document.docx') ?>';
            } else {
              $('#error-message').text(result.message).show();
            }
          } catch (e) {
            $('#error-message')
              .text('Unexpected response from server.')
              .show();
          }
        },
        error: function (xhr, status, error) {
          $('#error-message')
            .text('An error occurred: ' + error)
            .show();
        },
        complete: function () {
          $('#processButton')
            .prop('disabled', false)
            .text('Process Document');
        }
      });
    });
  </script>
</body>

</html>