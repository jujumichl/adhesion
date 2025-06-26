

const editModaleString = `
        <div class="modal fade" id="myModalLogin" role="dialog" data-bs-backdrop="static"
                data-bs-keyboard="false" >
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                        <h5 class="modal-title">Exporter la liste</h5>
                    </div>
                    <div class="row modal-body" id="modalbodyPack">
                        <p>Some text in the modal.</p>

                    </div>

                </div>

            </div>
        </div>
`;

// TODO : Manage callback
async function exportPersonListViewDisplay(htlmPartId, personListJSON) {

    //  let params = strParams.split(",");
    // let htlmPartId = params[0].trim();
    // console.log(JSON.stringify(param2));
    let personList = JSON.parse(personListJSON);
    // let mo_id = params[1].trim()
    //  let apiKey = params[2].trim()
    // let apiUrl = params[3].trim()
    // console.log("display Modale : " + htlmPartId + " - mo_id:" + mo_id);
    // *** Variable that keeps the modal object
    let editModal = null;

    try {
        // *** Display main part of the page
        jQuery("#" + htlmPartId).append(editModaleString);

        // *** Get data needed
        // let currentMO = await getMaterialOrder(mo_id, apiKey, apiUrl);
        // await getProducts(apiKey, apiUrl);

        // let produceLine = currentMO.lines.filter((line) => line.role == "toproduce");
        // let consumeLines = currentMO.lines.filter((line) => line.role == "toconsume");

        let outpuStr = '';
        outpuStr = `                             
       <form id="formPack" method="get"  href="'.DOL_URL_ROOT.'/custom/candleprod/candleprodindex.php">            
            
       <!-- *** Input to fill the produced PDV qty -->
            <h1 class= "modal-title fs-5" id= "exampleModalLabel"></h1>     
            <div class= "mb-3 row">
                <div class="col-8">
                  </div>
                <div class="col-sm-4">
                 </div>
             </div>
            <hr/>              

       <!-- *** Input to fill the consumed PDI qty -->
            

        <h1 class="modal-title fs-5" id="exampleModalLabel">Produits PDI consommés</h1>`;
        personList.map((person, index) => {
            outpuStr += `
            <div class= "mb-3 row" >
                <div class="col-8">
                    <label for="' class= " form-label" > `+ person.per_nom + ` ` + person.per_prenom + ` </label >
                </div >
                <div class="col-4">
            <!--     <input type="number" class="form-control input-sm pull-right text-end" name="ConsumedPDIQty${index}" id="ConsumedPDIQty${index}" placeholder="" value="" />
                    <input type="hidden" class="form-control col-sm-10 " name="ConsumedPDImoid${index}" id="FormControlInput"'.$j.' placeholder= "" value=""/>
                -->
                </div>
            </div > `
        });

        outpuStr += `
             <div class="modal-footer">
                   <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                  <input type="hidden" name="action" value= "packgenerateMOPDV"/>
                  <input type="hidden" name="moid" value= ""/>
                   <button type="submit" id="btnSave" class="btn btn-primary" form="" >Sauver</button>
                  <input type="hidden" id="ConsumedPDIlineNb" value= ""/>            
            </div>  
        </form >
            `;
        // *** Display string
        document.querySelector("#modalbodyPack").innerHTML = outpuStr;

        document.querySelector("#btnSave").onclick = async function (event) {
            document.getElementById('formPack').submit();
            console.log("Save clicked");
        };

        // document.querySelector("#ProducedQty").addEventListener("input", function (event) {
        //     console.log("ProducedQtymoid has changed : " + this.value);
        //     let ratio = 1;
        //     let orginalQty = document.querySelector("#ProducedQtyOld2").value;
        //     document.querySelector("#ProducedQtyOld2").value = this.value;
        //     if (this.value != null && this.value != 0 && orginalQty != 0) {
        //         ratio = this.value / orginalQty;
        //         console.log("Ratio :" + ratio);
        //     } else {
        //         console.log("Pas de calcul");
        //     }

        //     let pdilinesNb = document.querySelector("#ConsumedPDIlineNb").value;
        //     for (let line = 0; line < pdilinesNb; line++) {
        //         let oldValue = document.querySelector("#ConsumedPDIQty" + line).value;
        //         let newValue = oldValue * ratio;

        //         document.querySelector("#ConsumedPDIQty" + line).value = newValue;
        //         console.log("ConsumedPDIQty" + line + " - " + oldValue + " - " + newValue);
        //         //document.querySelector(v).value 
        //     }
        // });

        // *** Initialisation
        $(document).ready(function () {
            editModal = new bootstrap.Modal(document.querySelector("#myModalLogin"))
            editModal.show({ backdrop: 'static', keyboard: false });
        });


    } catch (error) {
        // console.log(`Error: ${error} `);
        document.querySelector("#" + htlmPartId).innerHTML = `<div class="alert alert-danger" style = "margin-top:30px" role = "alert" > ${error}</div > `;
    }
}

// /**
//  *
//  * @param {*} mo_id
//  * @returns
//  */
// async function getMaterialOrder(mo_id, getAPIKey, getAPIPath) {

//     //   let getAPIKey = '4OldYZ1V4zecXMvDRh7o9046pWpF6Oa1'
//     //   let getAPIPath = "http://localhost/dolibarr_prodchoco/api/index.php/"
//     console.log("getMO");

//     var wsUrl = `${getAPIPath}mos/${mo_id}?DOLAPIKEY=${getAPIKey}`;
//     let params = `&sortorder=ASC&limit=100&active=1`;
//     let responsefr = await fetch(wsUrl + params);

//     if (responsefr.ok) {
//         // *** Get the data and save in the sessionStorage
//         const data = await responsefr.json();
//         // console.log(JSON.stringify(data));
//         sessionStorage.setItem("materialOdrer", JSON.stringify(data));
//         return (data);

//     } else {
//         console.log(`intakePlaces Error : ${JSON.stringify(responsefr)}`);
//         throw new Error("getProdintakePlacesucts Error message : " + responsefr.status + " " + responsefr.statusText);
//     }

// }
// /**
//  *
//  * @param {*} getAPIKey
//  * @param {*} getAPIPath
//  * @returns
//  */
// async function getProducts(getAPIKey, getAPIPath) {

//     // let getAPIKey = '4OldYZ1V4zecXMvDRh7o9046pWpF6Oa1'
//     //  let getAPIPath = "http://localhost/dolibarr_prodchoco/api/index.php/"
//     console.log("getProducts Service start");

//     var wsUrl = `${getAPIPath}products?DOLAPIKEY=${getAPIKey}&limit=5000`;
//     let responsefr = await fetch(wsUrl);

//     if (responsefr.ok) {
//         // *** Get the data and save in the sessionStorage
//         let data = await responsefr.json();
//         sessionStorage.setItem("products", JSON.stringify(data));

//         return (data);
//     } else {
//         console.log(`getProducts Error : ${JSON.stringify(responsefr)}`);
//         throw new Error("getProducts Error message : " + responsefr.status + " " + responsefr.statusText);
//     }

// }
// function getProduct(prod_id) {

//     console.log("getProduct : " + prod_id)
//     let productsJson = sessionStorage.getItem("products");
//     let products = JSON.parse(productsJson);

//     let trouve = products.find(product => product.id == prod_id);

//     return trouve
// }



