<!-- Admin Panel -->
<div class="container admin-panel mt-5 ms-5">
    <h1 class="my-4">Settings</h1>

    <!-- Form to Add Project -->
    <div class="mb-4">
        <h3>Manutenzione Sito web</h3>
        <div class="mb-4">



            <form method="POST" action="/admin/settings" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Sito web in manutenzione</label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" name="check" type="checkbox" role="switch" id="flexSwitchCheckChecked" <?php echo ($env === 'true') ? 'checked' : '' ?>>
                    <label class="form-check-label" for="flexSwitchCheckChecked">fai il Check se vuoi andare in stato di manutenzione</label>
                </div>

                <button type="submit" class="btn btn-primary mt-5">Invia</button>
            </form>

        </div>
    </div>
</div>