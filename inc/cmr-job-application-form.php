<?php
// Job Application Form Component
// Shortcode: [cmr_job_application_form]

add_shortcode('cmr_job_application_form', 'cmr_job_application_form_shortcode');

function cmr_job_application_form_shortcode($atts) {
    ob_start();
    
    // Attempt to get the job title dynamically, fallback to generic
    $job_title = "Senior Research Analyst";
    if ( is_singular('quanto_job') ) {
        $job_title = get_the_title();
    }
    ?>
    <style>
        .cmr-job-form-wrapper {
            font-family: 'Instrument Sans', sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
            color: #111;
        }

        .cmr-job-form-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .cmr-job-form-header h2 {
            font-size: 32px;
            font-weight: 700;
            letter-spacing: -1px;
            margin-bottom: 15px;
            color: #111;
        }

        .cmr-job-form-header p {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
            max-width: 700px;
            margin: 0 auto;
        }

        .cmr-job-form-section {
            margin-bottom: 40px;
        }

        .cmr-job-form-section h3 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #111;
            letter-spacing: 0px;
        }

        /* Drag and Drop Zone */
        .cmr-upload-zone {
            border: 1px dashed #a0aabf;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            background: #fafafa;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .cmr-upload-zone:hover, .cmr-upload-zone.dragover {
            background: #f0f0ff;
            border-color: #6B46C1;
        }

        .cmr-upload-zone input[type="file"] {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            width: 100%; height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .cmr-upload-icon {
            margin-bottom: 15px;
            color: #111;
        }

        .cmr-upload-text {
            font-size: 15px;
            color: #333;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .cmr-upload-subtext {
            font-size: 13px;
            color: #777;
            margin-bottom: 15px;
        }

        .cmr-upload-browse {
            color: #6B46C1;
            font-weight: 600;
            font-size: 14px;
        }

        /* Uploaded File Preview */
        .cmr-file-preview {
            display: none;
            background: #f7f7f7;
            border-radius: 8px;
            padding: 20px;
            align-items: center;
            margin-top: 15px;
        }

        .cmr-file-icon {
            background: #dc2626;
            color: #fff;
            width: 40px;
            height: 40px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        .cmr-file-details {
            flex: 1;
        }

        .cmr-file-name-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .cmr-file-name {
            font-size: 14px;
            font-weight: 500;
            color: #111;
        }

        .cmr-file-size {
            font-size: 12px;
            color: #777;
            margin-left: 10px;
        }

        .cmr-file-delete {
            cursor: pointer;
            color: #999;
            transition: color 0.3s;
        }

        .cmr-file-delete:hover {
            color: #ef4444;
        }

        .cmr-progress-bar-container {
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        .cmr-progress-bar {
            height: 100%;
            background: #6B46C1;
            width: 100%;
            border-radius: 2px;
        }

        .cmr-progress-text {
            font-size: 11px;
            color: #777;
            margin-left: 10px;
        }

        .cmr-progress-row {
            display: flex;
            align-items: center;
        }

        .cmr-progress-bar-wrap {
            flex: 1;
        }

        /* Form Grid */
        .cmr-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .cmr-form-col-full {
            grid-column: 1 / -1;
        }

        /* Inputs */
        .cmr-input-group {
            position: relative;
        }

        .cmr-input {
            width: 100%;
            background: #f7f7f7;
            border: 1px solid transparent;
            border-radius: 8px;
            padding: 16px 20px;
            font-size: 15px;
            font-family: 'Instrument Sans', sans-serif;
            color: #111;
            transition: all 0.3s ease;
        }

        .cmr-input::placeholder {
            color: #888;
        }

        .cmr-input:focus {
            outline: none;
            border-color: #6B46C1;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(107, 70, 193, 0.1);
        }

        /* Phone Input specific */
        .cmr-phone-group {
            display: flex;
            gap: 10px;
        }

        .cmr-phone-prefix {
            width: 80px;
            background: #f7f7f7;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            color: #555;
        }

        .cmr-phone-input {
            flex: 1;
        }

        /* Buttons */
        .cmr-form-actions {
            display: flex;
            gap: 20px;
            margin-top: 50px;
        }

        .cmr-btn {
            flex: 1;
            padding: 16px;
            border-radius: 30px;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Instrument Sans', sans-serif;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s ease;
            border: none;
        }

        .cmr-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .cmr-btn-outline {
            background: transparent;
            border: 1px solid #111;
            color: #111;
        }

        .cmr-btn-outline:hover:not(:disabled) {
            background: #f5f5f5;
        }

        .cmr-btn-primary {
            background: #6B46C1;
            color: #fff;
        }

        .cmr-btn-primary:hover:not(:disabled) {
            background: #5536a0;
        }

        /* Messages */
        .cmr-form-message {
            display: none;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 15px;
            text-align: center;
        }

        .cmr-form-success {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .cmr-form-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        @media (max-width: 768px) {
            .cmr-form-grid {
                grid-template-columns: 1fr;
            }
            .cmr-form-actions {
                flex-direction: column;
            }
        }
    </style>

    <div class="cmr-job-form-wrapper">
        <div class="cmr-job-form-header">
            <h2>Apply for <?php echo esc_html($job_title); ?></h2>
            <p>Please upload your latest resume and provide your professional details below to help us understand your background. Once submitted, our team will review your profile and get back to you shortly.</p>
        </div>

        <form class="cmr-job-application-form" id="cmrJobForm" enctype="multipart/form-data">
            
            <input type="hidden" name="action" value="cmr_submit_application">
            <input type="hidden" name="job_title" value="<?php echo esc_attr($job_title); ?>">

            <!-- Upload Resume Section -->
            <div class="cmr-job-form-section">
                <h3>Upload Resume *</h3>
                
                <div class="cmr-upload-zone" id="cmrUploadZone">
                    <input type="file" id="cmrResumeFile" name="resume" accept=".pdf,.doc,.docx,.jpg,.jpeg" required>
                    <div class="cmr-upload-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                    </div>
                    <div class="cmr-upload-text">Drag & drop your resume here</div>
                    <div class="cmr-upload-subtext">PDF, DOC, JPG (Max 5MB)</div>
                    <div class="cmr-upload-browse">Browse files</div>
                </div>

                <!-- Preview State -->
                <div class="cmr-file-preview" id="cmrFilePreview">
                    <div class="cmr-file-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </div>
                    <div class="cmr-file-details">
                        <div class="cmr-file-name-row">
                            <div>
                                <span class="cmr-file-name" id="cmrFileName">Username_Resume.pdf</span>
                                <span class="cmr-file-size" id="cmrFileSize">1.2 MB</span>
                            </div>
                            <div class="cmr-file-delete" id="cmrFileDelete">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="cmr-progress-row">
                            <div class="cmr-progress-bar-wrap">
                                <div class="cmr-progress-bar-container">
                                    <div class="cmr-progress-bar"></div>
                                </div>
                            </div>
                            <div class="cmr-progress-text">100%</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Basic Information Section -->
            <div class="cmr-job-form-section">
                <h3>Basic Information *</h3>
                <div class="cmr-form-grid">
                    <div class="cmr-input-group">
                        <input type="text" name="full_name" class="cmr-input" placeholder="Full Name" required>
                    </div>
                    <div class="cmr-input-group">
                        <input type="text" name="location" class="cmr-input" placeholder="Current Location" required>
                    </div>
                    <div class="cmr-input-group cmr-form-col-full">
                        <input type="email" name="email" class="cmr-input" placeholder="Enter your e-mail address" required>
                    </div>
                    <div class="cmr-phone-group cmr-form-col-full">
                        <div class="cmr-phone-prefix">+91</div>
                        <input type="tel" name="phone" class="cmr-input cmr-phone-input" placeholder="9876543210" required>
                    </div>
                </div>
            </div>

            <!-- Quick Details Section -->
            <div class="cmr-job-form-section">
                <h3>Quick Details *</h3>
                <div class="cmr-form-grid">
                    <div class="cmr-input-group">
                        <input type="text" name="experience" class="cmr-input" placeholder="Total Experience (in yrs)" required>
                    </div>
                    <div class="cmr-input-group">
                        <input type="text" name="salary" class="cmr-input" placeholder="Expected Salary" required>
                    </div>
                </div>
            </div>

            <!-- Optional Section -->
            <div class="cmr-job-form-section">
                <h3>Optional</h3>
                <div class="cmr-input-group">
                    <input type="url" name="portfolio" class="cmr-input" placeholder="Portfolio Link">
                </div>
            </div>

            <div id="cmrFormError" class="cmr-form-message cmr-form-error"></div>
            
            <!-- Actions -->
            <div class="cmr-form-actions">
                <button type="button" id="cmrCancelBtn" class="cmr-btn cmr-btn-outline" onclick="document.getElementById('cmrJobForm').reset(); document.getElementById('cmrFileDelete').click(); document.getElementById('cmrFormError').style.display='none';">Cancel</button>
                <button type="submit" id="cmrSubmitBtn" class="cmr-btn cmr-btn-primary">Submit Application</button>
            </div>

        </form>

        <div id="cmrFormSuccess" class="cmr-form-message cmr-form-success">
            <strong>Application Submitted!</strong><br>Thank you for applying. We have received your resume and our team will get back to you shortly.
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('cmrResumeFile');
            const dropZone = document.getElementById('cmrUploadZone');
            const filePreview = document.getElementById('cmrFilePreview');
            const fileNameDisplay = document.getElementById('cmrFileName');
            const fileSizeDisplay = document.getElementById('cmrFileSize');
            const fileDeleteBtn = document.getElementById('cmrFileDelete');
            const form = document.getElementById('cmrJobForm');
            const submitBtn = document.getElementById('cmrSubmitBtn');
            const cancelBtn = document.getElementById('cmrCancelBtn');
            const successMsg = document.getElementById('cmrFormSuccess');
            const errorMsg = document.getElementById('cmrFormError');

            const ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";

            // Handle Drag and Drop Visuals
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => dropZone.classList.add('dragover'), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => dropZone.classList.remove('dragover'), false);
            });

            // Handle File Selection
            fileInput.addEventListener('change', handleFiles);
            dropZone.addEventListener('drop', function(e) {
                let dt = e.dataTransfer;
                let files = dt.files;
                if(files.length > 0) {
                    fileInput.files = files; // Assign files to input
                    handleFiles({ target: { files: files } });
                }
            });

            function handleFiles(e) {
                const files = e.target.files;
                if (files.length > 0) {
                    const file = files[0];
                    
                    // Simple Validation
                    if (file.size > 5 * 1024 * 1024) {
                        alert("File exceeds 5MB limit. Please choose a smaller file.");
                        fileInput.value = '';
                        return;
                    }

                    // Show Preview, hide Dropzone
                    dropZone.style.display = 'none';
                    filePreview.style.display = 'flex';
                    
                    // Update details
                    fileNameDisplay.textContent = file.name;
                    fileSizeDisplay.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                }
            }

            // Handle File Deletion
            fileDeleteBtn.addEventListener('click', function() {
                fileInput.value = ''; // Clear input
                filePreview.style.display = 'none';
                dropZone.style.display = 'block';
            });

            // Form Submit via AJAX
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                errorMsg.style.display = 'none';
                submitBtn.disabled = true;
                submitBtn.textContent = 'Submitting...';
                cancelBtn.disabled = true;

                const formData = new FormData(form);

                fetch(ajaxurl, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        form.style.display = 'none';
                        successMsg.style.display = 'block';
                        
                        // Scroll to the success message so the user doesn't end up at the footer
                        successMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    } else {
                        errorMsg.textContent = data.data.message || 'An error occurred. Please try again.';
                        errorMsg.style.display = 'block';
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Submit Application';
                        cancelBtn.disabled = false;
                    }
                })
                .catch(error => {
                    errorMsg.textContent = 'A network error occurred. Please try again later.';
                    errorMsg.style.display = 'block';
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Submit Application';
                    cancelBtn.disabled = false;
                    console.error('Error:', error);
                });
            });
        });
    </script>
    <?php
    return ob_get_clean();
}
