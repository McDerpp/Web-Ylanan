<div id="editModal" class="modal">

    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>

        <div class="modal-header">
            <h2>Edit Program</h2>

        </div>
        <form id="edit-form" action="program_api.php" method="POST" onsubmit="submitForm(event)">
            <input type="hidden" name="progid" id="modalProgid">

            <div class="form-group">
                <label for="modalProgFullName">Full Name:</label>
                <input type="text" name="progfullname" id="modalProgFullName" required>
            </div>

            <div class="form-group">
                <label for="modalProgShortName">Short Name:</label>
                <input type="text" name="progshortname" id="modalProgShortName" required>
            </div>

            <div class="form-group">
                <label for="modalCollegeName">College:</label>
                <input type="text" name="college_name" id="modalCollegeName" required>
            </div>

            <div class="form-group">
                <label for="modalDepartmentName">Department:</label>
                <input type="text" name="department_name" id="modalDepartmentName" required>
            </div>

            <div class="modal-actions">
                <button type="submit" class="btn-save">Save Changes</button>
            </div>
        </form>
    </div>
</div>