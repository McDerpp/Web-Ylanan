<div class="modal" id="editModal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>

        <div class="modal-header">
            <h2>Edit Department</h2>
        </div>
        <form id="edit-form">
            <div class="form-group">
                <label for="edit-deptid">Department ID:</label>
                <input type="text" id="edit-deptid" required>
            </div>
            <div class="form-group">
                <label for="edit-deptfullname">Full Name:</label>
                <input type="text" id="edit-deptfullname" required>
            </div>
            <div class="form-group">
                <label for="edit-deptshortname">Short Name:</label>
                <input type="text" id="edit-deptshortname" required>
            </div>
            <div class="form-group">
                <label for="edit-college">College:</label>
                <select id="edit-college" name="edit-college" required>
                    <option value="" disabled selected>Select College</option>
                    <!-- Populate dynamically with JavaScript -->
                </select>
            </div>
            <div class="modal-actions">
                <button type="submit" class="btn-save">Save Changes</button>
            </div>
        </form>
    </div>
</div>