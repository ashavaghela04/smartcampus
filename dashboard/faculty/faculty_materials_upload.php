<?php
$pageTitle  = "Upload Materials";
$activePage = "faculty_materials_upload";

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/sidebar_faculty.php';
?>

<div class="shell">
  <?php include __DIR__ . '/../includes/topbar.php'; ?>
  
  <main class="container py-5">
    <section class="card shadow-lg p-4 rounded-3">
      <h1 class="mb-4 text-primary">📚 Upload Study Materials </h1>
      <p class="text-muted mb-4">Select the subject and upload study material (PDF, DOCX, PPT, etc.)</p>

      <!-- ✅ Upload Form -->
      <form id="uploadForm" enctype="multipart/form-data" class="needs-validation" novalidate>
        
        <!-- Subject Input -->
        <div class="mb-3">
          <label for="subject" class="form-label fw-bold">Choose Subject:</label>
          <input type="text" id="subject" name="subject" class="form-control" placeholder="e.g. Computer Networks" required>
          <div class="invalid-feedback">Please enter the subject name.</div>
        </div>

        <!-- File Input -->
        <div class="mb-3">
          <label for="material_file" class="form-label fw-bold">Upload File:</label>
          <input type="file" id="material_file" name="material_file" class="form-control" 
                 accept=".pdf,.doc,.docx,.ppt,.pptx" required>
          <div class="form-text">Allowed formats: PDF, DOCX, PPT</div>
          <div class="invalid-feedback">Please upload a valid file.</div>
        </div>

        <!-- Status Message -->
        <div id="statusMessage" class="my-3"></div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3" id="uploadBtn">
          <i class="fas fa-upload"></i> Upload
        </button>
      </form>
    </section>
  </main>
</div>

<script>
document.getElementById("uploadForm").addEventListener("submit", async function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const statusMsg = document.getElementById("statusMessage");
    const uploadBtn = document.getElementById("uploadBtn");

    // Show loading state
    uploadBtn.disabled = true;
    uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Uploading...';
    statusMsg.innerHTML = "";

    try {
        const response = await fetch("materials_upload_save.php", {
            method: "POST",
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            statusMsg.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
            form.reset();
        } else {
            statusMsg.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
        }
    } catch (error) {
        statusMsg.innerHTML = `<div class="alert alert-danger">⚠️ Error uploading file. Please try again.</div>`;
    } finally {
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = '<i class="fas fa-upload"></i> Upload';
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
