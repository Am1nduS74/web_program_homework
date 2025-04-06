<!DOCTYPE html>
<html lang="fr-FR">

<head>
    <meta charset="UTF-8">
    <title>Assimil Course Home</title>
    <link style="text/css" rel="stylesheet" href="css/global.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <script src="./scripts/utils.js"></script>
    <style>

        body { 
            max-width: 800px; /* This centers the content */
        }


        #auth-bar {
            position: fixed; /* Fixed position for the auth bar */
            top: 1.5rem; 
            right: 1.5rem; 
            z-index: 1000; /* Make sure it stays on top of other elements */
        }

        #auth-btn {
            background:rgb(36, 148, 79); /* Button color */
            color: white; /* Text color */
            border: none; /* No border */
            padding: 0.8rem 1.5rem; /* Padding for the button */
            border-radius: 4px; /* Rounded corners */
            cursor: pointer; /* Cursor become a pointer when hovering */
            transition: all 0.3s ease; /* Smooth transition for hover effect */
            font-size: 1rem; 
        }

        #auth-btn:hover {
            background: #000000; /* Darker when hovering */
            transform: translateY(-1px); /* Slight movement effect in the auth button */
        }

        h1 {
            margin: 3rem 0 0; /* Margin for the title */
            letter-spacing: -0.5px; /* Letter spacing*/
            color: #008000;
        }

        #course-container {
            margin: 2rem 0; /* Margin for the course container */
        }

        .course-list {
            list-style: none; /* Remove the default list style */
            padding: 0; /* Remove padding */
            margin: 0; /* Remove margin */
        }

        .course-item {
            background: white; /* Background color */
            border-radius: 8px; /* Rounded corners */
            margin: 1rem 0; /* Margin between items */
            padding: 1.5rem; /* Padding for items */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); 
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94); /* Smooth transition when hovering*/
        }

        .course-item:hover {
            background: #f9f9f9; /* Light gray background on hover */
            border: 1px solid #008000; /* Blue border on hover */
            transform: translateY(-2px); /* Lift effect on hover */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Deeper shadow when hovering */
        }

        .course-link {
            text-decoration: none; /* Remove underline from links */
            color: #2c3e50; /* Text color */
            display: flex; /* Flexbox for alignment */
            align-items: center; /* Center items vertically */
            justify-content: space-between; /* Space between items */
        }

        .loading {
            text-align: center; /* Center text */
            padding: 2rem; /* Padding for loading text */
            color: #7f8c8d; /* Loading text color */
            font-size: 1.1rem; /* Font size for loading text */
        }

        .error {
            background: #fff5f5; /* Light red background for error */
            border: 1px solid #ff4444; /* Red border */
            border-radius: 8px; /* Rounded corners */
            padding: 1.5rem; /* Padding for error message */
            margin: 2rem 0; /* Margin for error message */
            text-align: center; /* Center text */
        }

        .error button {
            margin-top: 1rem; /* Margin for button */
            padding: 0.5rem 1.5rem; /* Padding for button */
        }

        #search-input {
            width: 100%; /* Full width */
            padding: 0.8rem; /* Padding for input */
            margin: 1rem 0 2rem; /* Margin for input */
            border: 1px solid #ddd; /* Light gray border */
            border-radius: 4px; /* Rounded corners */
            font-size: 1rem; /* Font size */
            box-sizing: border-box; /* Include padding in width calculation */
            transition: all 0.3s ease; /* Smooth transition for focus effect */
            text-align: center; /* Center text */
        }

        #search-input:focus {
            outline: none; /* Remove default outline */
            border-color: #008000; /* Blue border on focus */
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1); /* Light blue shadow on focus */
        }
    </style>
</head>

<body>
    <div id="auth-bar">
        <button id="auth-btn">Login</button>
    </div>

    <h1>Assimil French Course</h1>

    <input type="text" id="search-input" placeholder="Search Courses (e.g. 'free', 'purchased', 'chapter 1')" />

    <div id="course-container">
        <div class="loading">Loading courses...</div>
    </div>

    <script>
        function checkLoginStatus() { // This function checks if the user is logged in or not
            const token = localStorage.getItem('token'); //get the token from local storage
            const authBtn = document.getElementById('auth-btn'); //get the auth button

            if (token) {
                authBtn.textContent = 'Logout'; //if user is logged in button text will be Logout
                authBtn.onclick = () => {
                    localStorage.removeItem('token'); //when clicked logout the user
                    window.location.reload(); //reload
                };
            } else {
                authBtn.textContent = 'Login';
                authBtn.onclick = () => window.location.href = 'login.php'; //Redirect to login page
            }
        }

        function filterCourses(searchTerm) { // This function filters the courses based on what we search
            const courseItems = document.querySelectorAll('.course-item');
            courseItems.forEach(item => {
                const link = item.querySelector('.course-link');
                const textContent = link.textContent.toLowerCase();
                item.style.display = textContent.includes(searchTerm) ? '' : 'none';
            });
        }

        function renderCourses(courses) { // This function renders the courses on the page
            const container = document.getElementById('course-container');
            container.innerHTML = '';

            const list = document.createElement('ul');
            list.className = 'course-list';

            courses.forEach(course => { 
                const listItem = document.createElement('li');
                listItem.className = 'course-item';

                const link = document.createElement('a');
                link.className = 'course-link';
                link.href = `content.php?course_id=${course.course_id}`;

                
                const titleText = `Assimil French Chapter ${course.course_id}`;
                link.appendChild(document.createTextNode(titleText));

                if (course.status === 1) { 
                    const freeTag = document.createElement('span');
                    freeTag.className = 'free-tag';
                    freeTag.textContent = 'FREE';
                    link.appendChild(freeTag);
                } else if (course.status === 2) {
                    const purchasedTag = document.createElement('span');
                    purchasedTag.className = 'purchase-tag';
                    purchasedTag.textContent = 'PURCHASED';
                    link.appendChild(purchasedTag);
                }

                listItem.appendChild(link);
                list.appendChild(listItem);
            });

            container.appendChild(list);
        }

        function showError(message) { // This function shows an error message on the page
            const container = document.getElementById('course-container');
            container.innerHTML = `
                <div class="error">
                    <strong>Error:</strong> ${message}
                    <button onclick="location.reload()">Reload</button>
                </div>
            `;
        }

        window.addEventListener('DOMContentLoaded', () => { // Wait for the DOM to load first before executing the script
            checkLoginStatus();
            
            document.getElementById('search-input').addEventListener('input', function(e) { 
                const searchTerm = e.target.value.trim().toLowerCase();
                filterCourses(searchTerm);
            });

            loadCourses()
                .then(courses => renderCourses(courses))
                .catch(err => showError(err.message));
        });
    </script>
</body>

</html>