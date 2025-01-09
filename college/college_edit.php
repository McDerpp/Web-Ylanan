<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <div class="modal-header">
            <h2>Edit College</h2>
        </div>
        <form id="edit-form" action="college_api.php" method="PUT">
            <input type="hidden" name="collid" id="modalCollId">
            <div class="form-group">
                <label for="collfullname">Full Name</label>
                <input type="text" id="modalCollFullname" name="collfullname" required>
            </div>
            <div class="form-group">
                <label for="collshortname">Short Name</label>
                <input type="text" id="modalCollShortname" name="collshortname" required>
            </div>
            <button type="submit" class="btn-save">Save</button>

        </form>
    </div>
</div>