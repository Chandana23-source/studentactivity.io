<?php
require_once 'config.php';
requireLogin();

if (!hasRole('teacher')) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View and Manage Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    <style>
        body {
            background: url('https://media.istockphoto.com/id/1021640814/ru/%D1%84%D0%BE%D1%82%D0%BE/%D0%B2%D0%B5%D1%80%D0%BD%D1%83%D1%82%D1%8C%D1%81%D1%8F-%D0%B2-%D1%88%D0%BA%D0%BE%D0%BB%D1%83-%D1%84%D0%BE%D0%BD-%D1%88%D0%BA%D0%BE%D0%BB%D1%8C%D0%BD%D1%8B%D0%B5-%D0%BF%D1%80%D0%B8%D0%BD%D0%B0%D0%B4%D0%BB%D0%B5%D0%B6%D0%BD%D0%BE%D1%81%D1%82%D0%B8-%D0%BD%D0%B0-%D1%87%D0%B5%D1%80%D0%BD%D0%BE%D0%B9-%D0%BC%D0%B5%D0%BB%D0%BE%D0%B2%D0%BE%D0%B9-%D0%B4%D0%BE%D1%81%D0%BA%D0%B5-%D0%BA%D0%B2%D0%B0%D1%80%D1%82%D0%B8%D1%80%D0%B0-%D0%BB%D0%B5%D0%B6%D0%B0%D0%BB%D0%B0.jpg?s=170667a&w=0&k=20&c=4uQahffOechKxKHrOSel8_lrGdMq5E1Dkgs9TLMgHKw=');
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
            max-width: 1200px;
            margin: auto;
        }
        h3 {
            color: #007bff;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #007bff;
            color: white;
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
        .download-btn {
            margin-top: 20px;
            text-align: right;
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
                <h3 class="text-center">View Student Details</h3>
                <div class="btn-group d-flex mb-4" role="group">
                    <button class="btn btn-secondary w-100" type="button" onclick="showDetails('placement')">Placement</button>
                    <button class="btn btn-secondary w-100" type="button" onclick="showDetails('internship')">Internship</button>
                    <button class="btn btn-secondary w-100" type="button" onclick="showDetails('beHonors')">BE Honors</button>
                    <button class="btn btn-secondary w-100" type="button" onclick="showDetails('additionalCourse')">Additional Courses</button>
                </div>
                <div id="detailsContent">
                    <!-- Details will be dynamically displayed here -->
                </div>
                <div class="download-btn">
                    <button class="btn btn-success" onclick="downloadDetails()">Download as CSV</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentType = '';

        async function showDetails(type) {
            currentType = type;
            try {
                const response = await fetch(`get_details.php?type=${type}`);
                const result = await response.json();
                
                const content = document.getElementById('detailsContent');
                
                if (!result.success) {
                    content.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
                    return;
                }
                
                if (result.data.length === 0) {
                    content.innerHTML = `<div class="alert alert-info">No records found for ${type}.</div>`;
                    return;
                }
                
                let html = '<div class="table-responsive"><table class="table table-bordered"><thead><tr>';
                
                // Generate table headers
                const headers = Object.keys(result.data[0]);
                headers.forEach(header => {
                    if (header !== 'id') {
                        html += `<th>${header.replace(/_/g, ' ').toUpperCase()}</th>`;
                    }
                });
                html += '</tr></thead><tbody>';
                
                // Generate table rows
                result.data.forEach(row => {
                    html += '<tr>';
                    headers.forEach(header => {
                        if (header !== 'id') {
                            let value = row[header];
                            if (header.includes('path')) {
                                value = `<a href="uploads/${value}" target="_blank">View File</a>`;
                            }
                            html += `<td>${value}</td>`;
                        }
                    });
                    html += '</tr>';
                });
                
                html += '</tbody></table></div>';
                content.innerHTML = html;
                
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('detailsContent').innerHTML = 
                    '<div class="alert alert-danger">An error occurred while fetching the details.</div>';
            }
        }

        async function downloadDetails() {
            if (!currentType) {
                alert('Please select a category first.');
                return;
            }
            
            try {
                window.location.href = `download_details.php?type=${currentType}&format=csv`;
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while downloading the file.');
            }
        }
    </script>
</body>
</html>