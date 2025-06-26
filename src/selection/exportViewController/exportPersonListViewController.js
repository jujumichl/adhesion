

const editModaleStringExport = `
        <div class="modal fade" id="myModalLogin" role="dialog" data-bs-backdrop="static"
                data-bs-keyboard="false" >
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                        <h5 class="modal-title">Exporter les personnes</h5>
                    </div>
                    <div class="row modal-body" id="modalbodyPackinner">
                        <p>Some text in the modal.</p>

                    </div>

                </div>

            </div>
        </div>
`;

// TODO : Manage callback
async function exportPersonListViewController(htlmPartId) {

    // *** Variable that keeps the modal object
    let editModal = null;

    let personsList = JSON.parse(getPersonListFromDom());

    try {
        // *** Display main part of the page
        document.querySelector("#" + htlmPartId).innerHTML = editModaleStringExport;

        //  let brevoLists = await getBrevoLists();
        let outpuStr = '';
        outpuStr = `                             
       <form id="formPack" method="get"  href="'.DOL_URL_ROOT.'/custom/candleprod/candleprodindex.php">            
            
       <!-- *** Input to fill the produced PDV qty --> 
            <div class= "mb-3 row">
                <div class="col-12">
                Cette fonction permet d'exporter la liste de personnes dans le clipboard/presse papier.
                </div>
             </div>
            </hr>`;
        outpuStr += `<div class= "row"><div class= "col-12">`;
        personsList.map((person, index) => {
            outpuStr += person.per_nom + ", ";
        });
        outpuStr += `</div></div>`;

        outpuStr += `
             <div class="modal-footer">
                   <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                  <input type="hidden" name="action" value= "packgenerateMOPDV"/>
                  <input type="hidden" name="moid" value= ""/>
                   <button type="submit" id="btnSave" class="btn btn-outline-secondary" form="" >Envoyer</button>
                  <input type="hidden" id="ConsumedPDIlineNb" value= ""/>            
            </div>  
        </form >
            `;
        // *** Display string
        document.querySelector("#modalbodyPackinner").innerHTML = outpuStr;

        document.querySelector("#btnSave").onclick = async function (event) {
            await putToClipboard(JSON.stringify(personsList))
            console.log("Send to clipboard");
            editModal.hide();
        };

        editModal = new bootstrap.Modal(document.querySelector("#myModalLogin"))
        editModal.show({ backdrop: 'static', keyboard: false });


    } catch (error) {
        console.log(`Error: ${error} `);
        //  document.querySelector("#" + htlmPartId).innerHTML = `<div class="alert alert-danger" style = "margin-top:30px" role = "alert" > ${error}</div > `;
    }
}


function getPersonListFromDom() {
    let service = '';
    let elem = document.querySelector('.service-container');

    service = elem.dataset.service;

    return service;
}

async function putToClipboard(text) {

    const type = "text/plain";
    // const clipboardItemData = {
    //     [type]: text,
    // };
    // const clipboardItem = new ClipboardItem(clipboardItemData);
    // await navigator.clipboard.write([clipboardItem]);

    navigator.clipboard.writeText(text)
}


