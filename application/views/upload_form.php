<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document Processor - Upload Template</title>
  <!-- Include Bootstrap CSS -->
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

    .drag-drop-area {
      border: 2px dashed #007bff;
      border-radius: 8px;
      padding: 20px;
      text-align: center;
      background-color: #e9ecef;
      cursor: pointer;
    }

    .drag-drop-area.drag-over {
      background-color: #d1ecf1;
    }

    .drag-drop-area input {
      display: none;
    }

    #loadingMessage {
      display: none;
      margin-top: 20px;
      font-size: 16px;
      color: #007bff;
      text-align: center;
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
        <form id="uploadForm" action="DocumentProcessorController/upload" method="post" enctype="multipart/form-data">
          <!-- Drag and Drop for Template -->
          <div class="form-group">
            <label for="templateFile">Document Template</label>
            <div id="dragDropTemplate" class="drag-drop-area">
              Drag and Drop your Template File here or Click to Browse
              <input type="file" id="templateFile" name="template_file" accept=".doc,.docx,.rtf,.odt,.xls,.xlsx,.pdf"
                required>
            </div>
            <small class="form-text text-muted">Supported formats: .doc, .docx, .rtf, .odt, .xls, .xlsx, .pdf</small>
          </div>

          <!-- Drag and Drop for JSON -->
          <div class="form-group">
            <label for="dataFile">Data JSON File</label>
            <div id="dragDropJson" class="drag-drop-area">
              Drag and Drop your JSON File here or Click to Browse
              <input type="file" id="dataFile" name="data_file" accept=".json" required>
            </div>
            <small class="form-text text-muted">Upload the JSON file containing data for placeholder
              replacement.</small>
          </div>

          <!-- Submit Button -->
          <div class="text-center">
            <button type="submit" class="btn btn-primary btn-block" id="processButton">Process Document</button>
          </div>
        </form>
        <!-- Loading Message -->
        <div id="loadingMessage">
          <span>Processing your document, please wait...</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Include Bootstrap JS and jQuery -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Drag-and-Drop File Handling
    function setupDragAndDrop(areaId, fileInputId) {
      const dragDropArea = document.getElementById(areaId);
      const fileInput = document.getElementById(fileInputId);

      dragDropArea.addEventListener('click', () => fileInput.click());

      dragDropArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        dragDropArea.classList.add('drag-over');
      });

      dragDropArea.addEventListener('dragleave', () => {
        dragDropArea.classList.remove('drag-over');
      });

      dragDropArea.addEventListener('drop', (e) => {
        e.preventDefault();
        dragDropArea.classList.remove('drag-over');
        if (e.dataTransfer.files.length > 0) {
          fileInput.files = e.dataTransfer.files;
          dragDropArea.textContent = e.dataTransfer.files[0].name;
        }
      });

      fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
          dragDropArea.textContent = fileInput.files[0].name;
        }
      });
    }

    // Initialize Drag-and-Drop for both areas
    setupDragAndDrop('dragDropTemplate', 'templateFile');
    setupDragAndDrop('dragDropJson', 'dataFile');

    // Handle Process Document Button Click
    document.getElementById('uploadForm').addEventListener('submit', (e) => {
      const loadingMessage = document.getElementById('loadingMessage');
      const processButton = document.getElementById('processButton');

      // Show loading message
      loadingMessage.style.display = 'block';

      // Disable the button to prevent duplicate submissions
      processButton.disabled = true;

      // Optionally, you can further validate or display form data here.
    });
  </script>
</body>

</html>