<div id="edit-modal" class="modal">
    <span class="close" onclick="closeModal()">&times;</span>
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>

        <div class="modal-header">
            <h2>Edit Student</h2>
        </div>

        <form id="edit-form" action="student_api.php" method="POST" onsubmit="submitForm(event)">
            <input type="hidden" name="student_id" id="edit-id">

            <div class="form-group">
                <label for="edit-lastname">Last Name:</label>
                <input type="text" name="last_name" id="edit-lastname" required>
            </div>

            <div class="form-group">
                <label for="edit-firstname">First Name:</label>
                <input type="text" name="first_name" id="edit-firstname" required>
            </div>

            <div class="form-group">
                <label for="edit-middlename">Middle Name:</label>
                <input type="text" name="middle_name" id="edit-middlename" required>
            </div>

            <div class="form-group">
                <label for="college">College:</label>
                <select name="college" id="college" required>
                    <option value="" disabled selected>Select College</option>
                    <!-- Colleges will be dynamically populated here -->
                </select>
            </div>

            <div class="form-group">
                <label for="program">Program:</label>
                <select name="program" id="program" required>
                    <option value="" disabled selected>Select Program</option>
                    <!-- Programs will be dynamically populated here -->
                </select>
            </div>

            <div class="form-group">
                <label for="edit-year">Year:</label>
                <input type="number" name="year" id="edit-year" required>
            </div>

            <div class="modal-actions">
                <button type="submit" class="btn-save">Save Changes</button>
            </div>
        </form>
    </div>

</div>