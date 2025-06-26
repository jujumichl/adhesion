
import { getPersonforCriteria, getSubList, addList } from './selectionService.js'
import { displayPersonList, displaySelectionjsContent } from './selectjsViewController.js'
const editModaleString = `
        <div class="modal fade " id="myModalgetPerson" role="dialog" data-bs-backdrop="static"
                data-bs-keyboard="false" >
            <div class="modal-dialog modal-xl">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                        <h5 class="modal-title">Ajouter des personnes à la liste en cours </h5>
                    </div>
                    <div class="row modal-body" id="modalbodyPack">
                        <p>Some text in the modal.</p>
                    </div>

                <div class="modal-footer">
              </div>

                </div>
            </div>
        </div>
`;

// TODO : Manage callback
export async function getpersonsViewDisplay(htlmPartId) {

    let personList = "";
    let editModal = null;

    try {
        // *** Display main part of the page
        jQuery("#" + htlmPartId).append(editModaleString);

        let personsList = [] // await getPersonforCriteria("");
        // console.log(json_encode(personsList));
        let outpuStr = `
        <!--<h1 class="modal-title fs-5" id="exampleModalLabel">Listes des personnes</h1>-->
        <div class="row">
            <div class="col-12">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="" id="searchString" name="searchString" aria-label="" aria-describedby="" value="">
                    <button type="" value=""  class="btn btn-outline-secondary" id="buttonSearch">Chercher</button>
                </div>
            </div>
        </div>
 
        <div class="row ">
            <div class="col-12 d-flex flex-row-reverse">
                <button type="submit" class="btn btn-outline-secondary  btn-sm" id="btnSaveList" class="btn btn-primary" style= "margin-left:3px"  >Sauver</button>
                <button type="button" class="btn btn-outline-secondary  btn-sm" data-bs-dismiss="modal" style= "margin-left:3px" >Annuler</button>

            </div>
        </div>
 
        <div id="searchResultPart"></div>
        `;

        // outpuStr += displayResultList(personsList);

        // *** Display string
        outpuStr += displayResultList(personsList);

        document.querySelector("#modalbodyPack").innerHTML = outpuStr;

        // document.querySelector("#btnSave").onclick = async function (event) {
        //     document.getElementById('formPack').submit();
        //     console.log("Save clicked");
        // };

        document.querySelector("#buttonSearch").onclick = async function (event) {
            let personsList = await getPersonforCriteria(document.querySelector("#searchString").value);
            console.log("buttonSearch");
            let outpuStr = displayResultList(personsList);
            document.querySelector("#searchResultPart").innerHTML = outpuStr;
            // getpersonsViewDisplay(htlmPartId);
        };

        document.querySelector("#btnSaveList").onclick = async function (event) {
            // let personsList = await getPersonforCriteria(document.querySelector("#searchString").value);
            console.log("btnSaveList");

            let subList = getCheckedPersonList();
            addList(subList);
            // *** Display list
            // let outpuStr = displayResultList(personsList);
            // document.querySelector("#searchResultPart").innerHTML = outpuStr;
            editModal.hide();
            displaySelectionjsContent("mainActiveSection");

        };

        // *** Initialisation
        // $(document).ready(function () {
        editModal = new bootstrap.Modal(document.querySelector("#myModalgetPerson"))
        editModal.show({ backdrop: 'static', keyboard: false });
        // });


        // *** Main checkboxe change => change person checkbox
        document.getElementById("mainCheckSub").addEventListener('change', function (e) {
            const cbox = document.querySelectorAll(".personcheckSub");
            for (let i = 0; i < cbox.length; i++) {
                cbox[i].checked = e.target.checked;
            }
        });


    } catch (error) {
        // console.log(`Error: ${ error } `);
        document.querySelector("#" + htlmPartId).innerHTML = `<div class="alert alert-danger" style = "margin-top:30px" role = "alert" > ${error}</div > `;
    }
}





/**
 * 
 * @returns 
 */
function getCheckedPersonList() {
    let returnTab = [];
    let cbox = document.querySelectorAll(".personcheckSub");

    for (let i = 0; i < cbox.length; i++) {
        if (cbox[i].checked === true) {
            console.log("checked = " + + cbox[i].dataset.service + "</br>");
            returnTab.push(JSON.parse(cbox[i].dataset.service));
        }
    }
    return returnTab;
}
/**
 * 
 * @param {*} personsList 
 * @returns 
 */
function displayResultList(personsList) {
    let outpuStr = `       </hr>
     <div class="col-12 "> ${personsList.length} résultats dans la liste
    </div >

 
    <table class="table table-striped">
    <thead>
        <tr>
            <th scope="col">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="mainCheckSub">
                </div>
            </th>
            <th scope="col">Nom Prénom</th>
            <th scope="col">Email</th>

        </tr>
    </thead>
    <tbody>`;

    personsList.map((person, index) => {
        outpuStr += `<tr>
        <td> <div class="form-check">
            <input class="form-check-input personcheckSub" type="checkbox" data-service='${JSON.stringify(person)}'>
        </div>
        <td>  ${person.per_nom} ${person.per_prenom}</td>
        <td> ${person.per_email}</td>
         
         </tr>`;
    });
    outpuStr += `</tbody>
    </table>
    `;
    return outpuStr;
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

//     var wsUrl = `${ getAPIPath } mos / ${ mo_id }?DOLAPIKEY = ${ getAPIKey } `;
//     let params = `& sortorder=ASC & limit=100 & active=1`;
//     let responsefr = await fetch(wsUrl + params);

//     if (responsefr.ok) {
//         // *** Get the data and save in the sessionStorage
//         const data = await responsefr.json();
//         // console.log(JSON.stringify(data));
//         sessionStorage.setItem("materialOdrer", JSON.stringify(data));
//         return (data);

//     } else {
//         console.log(`intakePlaces Error: ${ JSON.stringify(responsefr) } `);
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

//     var wsUrl = `${ getAPIPath } products ? DOLAPIKEY = ${ getAPIKey }& limit=5000`;
//     let responsefr = await fetch(wsUrl);

//     if (responsefr.ok) {
//         // *** Get the data and save in the sessionStorage
//         let data = await responsefr.json();
//         sessionStorage.setItem("products", JSON.stringify(data));

//         return (data);
//     } else {
//         console.log(`getProducts Error: ${ JSON.stringify(responsefr) } `);
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



