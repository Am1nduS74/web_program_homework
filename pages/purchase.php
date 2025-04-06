<!DOCTYPE html>
<html lang="fr-FR"> <!-- This is the HTML document for the purchase page of Assimil French courses. -->

<head>
    <meta charset="UTF-8">
    <title>Purchase Courses</title>
    <link style="text/css" rel="stylesheet" href="css/global.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"> <!-- Link to Google Fonts for Poppins font. -->
    <script src="./scripts/utils.js"></script>
    <style>
        
        .purchase-container {
            position: relative; /* Position relative for absolute children */
            max-width: 800px; /* Max width for the container */
            margin: 2rem auto;
            padding: 2rem; /* Padding for the container */
            background: white; /* Background color */
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .course-list {
            margin: 2rem 0; /* Margin for the course list */
        }

        .course-item {
            display: flex; /* Flexbox for alignment */
            align-items: center; /* Center items vertically */
            padding: 1rem;
            margin: 0.5rem 0;
            background: var(--background-light); /* Light background */
            border-radius: 4px; /* Rounded corners */
            transition: all 0.3s ease; /* Smooth transition for hover effect */
        }

        .course-item:hover {
            transform: translateX(5px); /* Slight movement effect on hover */
            box-shadow: 0 2px 6px rgba(52, 152, 219, 0.1);
        }

        input[type="checkbox"] {
            width: 1.2rem; /* Checkbox width */
            height: 1.2rem; /* Checkbox height */
            margin-right: 1rem;
        }

        .purchase-btn,
        .refund-btn {
            width: 100%; /* Full width for buttons */
            padding: 1rem 2rem; /* Padding for buttons */
            margin-top: 1rem; /* Margin for spacing */
            border: none; /* No border */
            border-radius: 4px; /* Rounded corners */
            cursor: pointer; /* Pointer cursor on hover */
            font-size: 1.3rem; /* Font size for buttons */
            transition: background 0.3s ease; 
            color: white; /* Text color */
        }

        .purchase-btn {
            background: #008000; /* Green background */
        }

        .purchase-btn:hover {
            background: #000000; /* Darker on hover */
        }

        .refund-btn {
            background: #e74c3c; /* Red background */
        }

        .refund-btn:hover {
            background: #000000; /* Darker on hover */
        }

        .validation-message {
            color: #ff4444; /* Red color for error messages */
            text-align: center; /* Center text */
            padding: 1rem; /* Padding for error messages */
            margin: 1rem 0; /* Margin for spacing */
        }

        #username {
            text-align: center; /* Center text */
            font-size: 2rem;
        }

        label {
            position: relative; /* Position relative for absolute children */
        }

        label>span {
            position: absolute; /* Position absolute for tags */
            right: 0;
            top: 50%; /* Center vertically */
            transform: translateY(-50%); /* Center vertically */
            margin-right: 30px;
        }
    </style>
</head>

<body>
    <div class="purchase-container">
        <div class="close-btn" onclick="window.location.href='/'">×</div> <!-- Close button to redirect to home page -->
        <h1 id="username"></h1>
        <div class="course-list" id="courseList">
            <div class="loading">Loading courses...</div>
        </div>
        <div class="validation-message hidden" id="errorMessage"></div> <!-- Error message container -->
        <button class="refund-btn" id="refundBtn" onclick="handleRefund()">Request Refund</button>
        <button class="purchase-btn" onclick="handlePurchase()">Purchase Selected Courses</button>
    </div>
    <script src="scripts/utils.js"></script> <!-- Include utility functions for error handling and loading courses -->
    <script>

        async function renderCourses(data) { // This function renders the list of courses on the page
            try {
                const container = document.getElementById('courseList');
                container.innerHTML = data.map(course => `
                    <label class="course-item">
                        <input type="checkbox" name="course_id" value="${course.course_id}">
                        Assimil French Chapter ${course.course_id}
                        ${course.status === 1
                        ? '<span class="free-tag">FREE</span>'
                        : course.status === 2
                            ? '<span class="purchase-tag">PURCHASED</span>'
                            : ''
                    }
                    </label>
                `).join('');
            } catch (error) {
                showError('Failed to load courses');
            }
        }


        async function handlePurchase() { // This function handles the purchase of selected courses
            const checkboxes = document.querySelectorAll('input[name="course_id"]:checked');
            const courseIds = Array.from(checkboxes).map(cb => cb.value);

            if (courseIds.length === 0) { // Check if a chapter is selected
                return showError('Please select at least one course');
            }

            try {
                const response = await fetch('/api/purchase/add', { // This function sends a POST request to purchase the selected courses
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    },
                    body: JSON.stringify({ course_ids: courseIds }) // Send the selected course IDs in the request body
                });

                const result = await response.json(); 

                if (response.ok) {
                    alert('Purchase successful!'); 
                    window.location.href = '/';
                } else {
                    showError(result.msg || 'Purchase failed');
                }
            } catch (error) {
                showError('Network error');
            }
        }

        async function handleRefund() { // This function handles the refund request for selected courses
            const checkboxes = document.querySelectorAll('input[name="course_id"]:checked');
            const courseIds = Array.from(checkboxes).map(cb => cb.value);

            if (courseIds.length === 0) {
                return showError('Please select at least one course for refund');
            }

            try {
                const response = await fetch('/api/purchase/remove', { 
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json', 
                        'Authorization': `Bearer ${localStorage.getItem('token')}` // Include the token in the request headers
                    },
                    body: JSON.stringify({ course_ids: courseIds }) // Send the selected course IDs in the request body
                });

                const result = await response.json();

                if (response.ok) {
                    alert('Refund request submitted successfully!');
                    window.location.reload();
                } else {
                    showError(result.msg || 'Refund request failed');
                }
            } catch (error) {
                showError('Network error while processing refund');
            }
        }

        window.addEventListener('DOMContentLoaded', () => { loadCourses().then(data => renderCourses(data)); });
        document.getElementById('username').textContent = "Hi " + localStorage.getItem('username') + ", please select courses to purchase"; 
    </script>
</body>

</html>