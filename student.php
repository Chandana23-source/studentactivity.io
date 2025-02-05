<?php
require_once 'config.php';
requireLogin();

if (!hasRole('student')) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    <style>
        body {
            background: url('https://static.vecteezy.com/system/resources/previews/005/948/321/non_2x/back-to-school-banner-with-hand-drawn-line-art-icons-of-education-science-objects-and-office-supplies-school-supplies-concept-of-education-background-free-vector.jpg');
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container-fluid {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }
        .btn-group .btn {
            margin-right: 5px;
        }
        h3 {
            color: #007bff;
            margin-bottom: 20px;
        }
        .back-link {
            display: block;
            margin-bottom: 20px;
            text-align: right;
        }
        .back-link a {
            color: #007bff;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        .alert {
            display: none;
            margin-top: 20px;
        }
        #printSection {
            display: none;
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            #printSection, #printSection * {
                visibility: visible;
            }
            #printSection {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="back-link">
                    <a href="logout.php">Logout</a>
                </div>
                <h3 class="text-center">Enter the Details</h3>
                <div class="btn-group d-flex mb-4" role="group">
                    <button class="btn btn-secondary w-100" type="button" onclick="showForm('placement')">Placement</button>
                    <button class="btn btn-secondary w-100" type="button" onclick="showForm('internship')">Internship</button>
                    <button class="btn btn-secondary w-100" type="button" onclick="showForm('beHonors')">BE Honors</button>
                    <button class="btn btn-secondary w-100" type="button" onclick="showForm('additionalCourse')">Additional Courses</button>
                </div>
                <form id="dynamicForm" role="form" enctype="multipart/form-data">
                    <!-- Dynamic form content will be inserted here -->
                </form>
                <div class="alert" role="alert"></div>
                <div id="printSection">
                    <!-- Print content will be inserted here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentFormType = '';

        function showForm(type) {
            currentFormType = type;
            const form = document.getElementById('dynamicForm');
            form.innerHTML = '';

            const commonFields = `
                <div class="form-group">
                    <label for="semester">Semester</label>
                    <input type="number" class="form-control" name="semester" required />
                </div>
                <div class="form-group">
                    <label for="section">Section</label>
                    <input type="text" class="form-control" name="section" required />
                </div>
            `;

            if (type === 'placement') {
                form.innerHTML = `
                    <input type="hidden" name="type" value="placement">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="name" required />
                    </div>
                    <div class="form-group">
                        <label for="usn">USN</label>
                        <input type="text" class="form-control" name="usn" required />
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" class="form-control" name="phone" required />
                    </div>
                    <div class="form-group">
                        <label for="company">Company</label>
                        <input type="text" class="form-control" name="company" required />
                    </div>
                    <div class="form-group">
                        <label for="ctc">CTC</label>
                        <input type="text" class="form-control" name="ctc" required />
                    </div>
                    <div class="form-group">
                        <label for="year">Year of Placement</label>
                        <input type="number" class="form-control" name="year" required />
                    </div>
                    <div class="form-group">
                        <label for="offer_letter">Offer Letter</label>
                        <input type="file" class="form-control-file" name="offer_letter" accept="application/pdf" required />
                    </div>
                    ${commonFields}
                    <button type="submit" class="btn btn-primary">Submit</button>
                `;
            } else if (type === 'internship') {
                form.innerHTML = `
                    <input type="hidden" name="type" value="internship">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="name" required />
                    </div>
                    <div class="form-group">
                        <label for="usn">USN</label>
                        <input type="text" class="form-control" name="usn" required />
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" class="form-control" name="phone" required />
                    </div>
                    <div class="form-group">
                        <label for="company">Company</label>
                        <input type="text" class="form-control" name="company" required />
                    </div>
                    <div class="form-group">
                        <label for="offer_letter">Offer Letter</label>
                        <input type="file" class="form-control-file" name="offer_letter" accept="application/pdf" required />
                    </div>
                    ${commonFields}
                    <button type="submit" class="btn btn-primary">Submit</button>
                `;
            } else if (type === 'beHonors') {
                form.innerHTML = `
                    <input type="hidden" name="type" value="beHonors">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="name" required />
                    </div>
                    <div class="form-group">
                        <label for="usn">USN</label>
                        <input type="text" class="form-control" name="usn" required />
                    </div>
                    <div class="form-group">
                        <label for="marks1">Marks for 1st Sem</label>
                        <input type="number" step="0.01" class="form-control" name="marks1" required />
                    </div>
                    <div class="form-group">
                        <label for="marks2">Marks for 2nd Sem</label>
                        <input type="number" step="0.01" class="form-control" name="marks2" required />
                    </div>
                    <div class="form-group">
                        <label for="marks3">Marks for 3rd Sem</label>
                        <input type="number" step="0.01" class="form-control" name="marks3" required />
                    </div>
                    <div class="form-group">
                        <label for="marks4">Marks for 4th Sem</label>
                        <input type="number" step="0.01" class="form-control" name="marks4" required />
                    </div>
                    <div class="form-group">
                        <label for="course">Course</label>
                        <input type="text" class="form-control" name="course" required />
                    </div>
                    <div class="form-group">
                        <label for="certificate">Certificate</label>
                        <input type="file" class="form-control-file" name="certificate" accept="application/pdf,image/*" required />
                    </div>
                    ${commonFields}
                    <button type="submit" class="btn btn-primary">Submit</button>
                `;
            } else if (type === 'additionalCourse') {
                form.innerHTML = `
                    <input type="hidden" name="type" value="additionalCourse">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="name" required />
                    </div>
                    <div class="form-group">
                        <label for="usn">USN</label>
                        <input type="text" class="form-control" name="usn" required />
                    </div>
                    <div class="form-group">
                        <label for="courses">Courses</label>
                        <input type="text" class="form-control" name="courses" required />
                    </div>
                    <div class="form-group">
                        <label for="year">Year of Completion</label>
                        <input type="number" class="form-control" name="year" required />
                    </div>
                    <div class="form-group">
                        <label for="duration">Duration of the Course</label>
                        <input type="text" class="form-control" name="duration" required />
                    </div>
                    <div class="form-group">
                        <label for="certificate">Certificate</label>
                        <input type="file" class="form-control-file" name="certificate" accept="application/pdf,image/*" required />
                    </div>
                    ${commonFields}
                    <button type="submit" class="btn btn-primary">Submit</button>
                `;
            }

            // Add form submission handler
            form.onsubmit = handleSubmit;
        }

        async function handleSubmit(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            
            try {
                const response = await fetch('submit_details.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                const alert = document.querySelector('.alert');
                alert.style.display = 'block';
                alert.className = `alert ${result.success ? 'alert-success' : 'alert-danger'}`;
                alert.textContent = result.message;
                
                if (result.success) {
                    // Create print view
                    const printSection = document.getElementById('printSection');
                    printSection.style.display = 'block';
                    
                    let printContent = `
                        <h4 class="text-center mb-4">Submission Receipt</h4>
                        <p><strong>Type:</strong> ${currentFormType}</p>
                    `;
                    
                    for (let [key, value] of formData.entries()) {
                        if (key !== 'type' && !key.includes('letter') && !key.includes('certificate')) {
                            printContent += `<p><strong>${key.charAt(0).toUpperCase() + key.slice(1)}:</strong> ${value}</p>`;
                        }
                    }
                    
                    printContent += `
                        <p><strong>Submission Date:</strong> ${new Date().toLocaleString()}</p>
                        <div class="text-center mt-4">
                            <button onclick="window.print()" class="btn btn-primary">Print Receipt</button>
                        </div>
                    `;
                    
                    printSection.innerHTML = printContent;
                    e.target.reset();
                }
            } catch (error) {
                console.error('Error:', error);
                const alert = document.querySelector('.alert');
                alert.style.display = 'block';
                alert.className = 'alert alert-danger';
                alert.textContent = 'An error occurred while submitting the form.';
            }
        }
    </script>
</body>
</html>