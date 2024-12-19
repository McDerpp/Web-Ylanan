<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Entry</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<body>
    <div class="container">
        <h2>New Student Entry</h2>
    <div id="message"><?php echo $message; ?></div>

        <form id="studentForm" method="POST">
            <label for="student_id">Student ID:</label>
            <input type="text" id="student_id" name="student_id" required>

            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>

            <label for="middle_name">Middle Name:</label>
            <input type="text" id="middle_name" name="middle_name">

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required>

            <label for="college">College:</label>
            <select id="college" name="college" required>
                <option value="" disabled selected>Select College</option>

            </select>

            <label for="program">Program:</label>
            <select id="program" name="program" required>
                <option value="" disabled selected>Select Program</option>
                <!-- Populate programs dynamically (if necessary) -->
            </select>

            <label for="year">Year:</label>
            <input type="text" id="year" name="year" required>

            <div class="buttons">
                <input type="submit" value="Save">
                <button type="reset">Clear Entries</button>
                <button type="button" onclick="window.location.href='dashboard.php';">Cancel</button>
            </div>
        </form>
        <div id="response"></div>
    </div>

    <script>
        let colleges = {}; // Global variable to store colleges data
        let programs = {};

        let collegeSelected;
        let programSelected;
        
        let collegePicked;
        let programPicked;
        



        // Function to load colleges from the server
        function loadColleges() {
            axios.get('get-data.php', {
                    params: {
                        type: 'colleges'
                    }
                })
                .then(function(response) {
                    console.log(response.data);
                    if (response.data && response.data.length > 0) {
                        const collegeSelect = document.getElementById('college');
                        response.data.forEach(function(college) {
                            const option = document.createElement('option');
                            option.value = college.collfullname; // Set college ID as the option value
                            option.textContent = college.collfullname; // Set college name as the option text
                            collegeSelect.appendChild(option);
                        });
                    } else {
                        console.error("No colleges found.");
                    }
                })
                .catch(function(error) {
                    console.error('Error loading colleges:', error);
                });
        }


        function loadPrograms(progFullName) {
    console.log("Loading programs for college full name:", progFullName);

    // Clear the program dropdown before fetching new data
    const programSelect = document.getElementById('program');
    programSelect.innerHTML = ''; // Clear current options

    // Add the default "Select Program" option
    const defaultOption = document.createElement('option');
    defaultOption.textContent = 'Select Program';
    defaultOption.disabled = true;
    defaultOption.selected = true;
    programSelect.appendChild(defaultOption);

    // Send a request to the backend to fetch programs for the selected college
    axios.get('get-data.php', {
        params: {
            type: 'programs', // Specify that we want to get programs
            progFullName: progFullName // Pass the college's full name
        }
    })
    .then(function(response) {
        console.log(response.data); // Log the response data

        // Check if programs are returned, if so, populate the dropdown
        if (response.data && response.data.length > 0) {
            response.data.forEach(function(program) {
                const option = document.createElement('option');
                option.value = program.progfullname; // Program ID
                option.textContent = program.progfullname; // Program Full Name
                programSelect.appendChild(option);
            });
        } else {
            // If no programs are found, show a "No programs available" option
            const noProgramsOption = document.createElement('option');
            noProgramsOption.value = '';
            noProgramsOption.textContent = 'No programs available';
            programSelect.appendChild(noProgramsOption);
        }
    })
    .catch(function(error) {
        console.error('Error fetching programs:', error);
        // Handle error case
        const errorOption = document.createElement('option');
        errorOption.textContent = 'Error loading programs. Please try again.';
        errorOption.disabled = true;
        programSelect.appendChild(errorOption);
    });
}





        // Handle the event when a college is selected
        function onCollegeChange(event) {
            const selectedCollegeId = event.target.value;
            loadPrograms(selectedCollegeId);
            collegePicked = colleges.find(college => college.collfullname == selectedCollegeID);
            console.log("test->",collegePicked);



            const programSelect = document.getElementById('program');


            // Reset the program dropdown
            programSelect.innerHTML = '<option value="" disabled selected>Select Program</option>';

            if (programs[selectedCollegeId] && programs[selectedCollegeId].length > 0) {
                programs[selectedCollegeId].forEach(function(program) {
                    const option = document.createElement('option');
                    option.value = program.progid;
                    option.textContent = program.progfullname;
                    programSelect.appendChild(option);
                });
            } else {
                const noProgramsOption = document.createElement('option');
                noProgramsOption.value = '';
                noProgramsOption.textContent = 'No programs available';
                programSelect.appendChild(noProgramsOption);
            }
        }




        document.addEventListener('DOMContentLoaded', function() {
            loadColleges();
            collegeSelect = document.getElementById('college');
            collegeSelect.addEventListener('change', onCollegeChange);
            console.log('College changed to:', collegeSelect.value);




            programSelected = document.getElementById('program');
            programSelected.addEventListener('change', function() {
        const selectedProgramId = programSelected.value; // Get the selected program ID
        const selectedOption = programSelected.options[programSelected.selectedIndex]; // Get the selected option
        const programPicked = selectedOption.text; // Get the program's fullname (text)

        if (selectedProgramId) {
            console.log('Program ID:', selectedProgramId); // Program ID (progid)
            console.log('Program Fullname:', programPicked); // Program Fullname (progfullname)
        }
    });



    

            document.getElementById('studentForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the form from submitting the usual way

    // Gather form data
    const formData = new FormData(this);

    // Log form data to console for debugging
    const formDataObj = {};
    formData.forEach((value, key) => {
        formDataObj[key] = value;
    });
    console.log('Form data:', formDataObj);
    
    // Send the form data using axios
    axios.post('save_student_info.php', formData)
        .then(function(response) {
            console.log(response.data);  // Log the response data from PHP
            document.getElementById('response').innerText = response.data.message;  // Display the message from PHP
        })
        .catch(function(error) {
            // Handle error
            console.error(error);
            document.getElementById('response').innerText = 'An error occurred: ' + error.message;
        });
});







        });
    </script>
</body>

</html>