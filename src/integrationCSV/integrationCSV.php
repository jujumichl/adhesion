<?php







/**************** VIEW ****************************** */
/**
 * Display integration form of the integration page
 */
function displayIntegrationCsv(){
    return '
    <div style="margin-top:100px">
    <div class="row">
        <div class="h5" style="color:#d07d29">Sélection</div>
        <hr/>
        <div class="col-6">
        <form action="index.php?uc=upload" method="post" enctype="multipart/form-data">
            <div class="input-group w-100 float-end">
                <input
                    type="file"
                    id="fileToUpload"
                    name="fileToUpload"
                    class="form-control"
                    placeholder="Choisir un fichier .csv"
                />
            </div>
        </div>
        <div class="col-6">
         <button type="submit" class="btn btn-outline-secondary">Lancer l\'intégration</button>        </div>
        </div>
        </form>
    </div>
    <div class="h6" style="color:#d07d29; margin-top:20px">Résultat de l\'intégration</div>
        <hr/>
        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt</br>
        Consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur</br>
        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt </br>
        Consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur </br>
</div>';
}