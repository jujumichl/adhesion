// *** Component ressources
import { getActivities, getPersonforCriteria } from './searchService.js'
import { headerViewDisplaySelectionjs, getAppPath, getappRelease } from '../shared/functions.js'
/**
 * when called from the url
 * get the parameters and launch the controller
 */
export async function startSearchController() {



  // *** Initialisations
  try {

  } catch (error) {
    document.querySelector("#messageSection").innerHTML = `<div class="alert alert-danger" style = "margin-top:30px" role = "alert" > ${error} - ${error.fileName}</br>${error.stack}  </div > `;
  }

  headerViewDisplaySelectionjs("menuSection")
  displaySearchContent("mainActiveSection");
}

/**
 * Display 
 * @param {*} htlmPartId 
 * @param {*} searchString : the string to searched in the database 
 */
export async function displaySearchContent(htlmPartId) {


  let activities = await getActivities();

  // *** Display the controller skeleton
  let outpuStr = `
    <!--< h1 class="modal-title fs-5" id = "exampleModalLabel" > Listes des personnes</h1 > -->
        <div class="row" style="margin-top:100px">
            <div class="h5" style="color:#d07d29">Recherche JS</div>
            <hr/>

            <div class="col-4">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="" id="searchString" name="searchString" aria-label="" aria-describedby="" value="">
                    <button type="" value=""  class="btn btn-outline-secondary" id="buttonSearch">Chercher</button>
                </div>
            </div>

            <div class="col-6" style="margin-right:5px">
               <div class="dropdown" >
                    <a class="btn btn-outline-secondary dropdown-toggle"  href="#" role="button" id="activiteList" data-bs-toggle="dropdown" aria-expanded="false">
                        Recherches définies
                    </a>

                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink" id="activityChoice">
                    `;
  activities.map((activity, index) => {
    outpuStr += `<li class="dropdown-item" id="${activity.act_id}">${activity.act_libelle}</li>`
  });

  outpuStr += `</ul>
                </div>
            <!--</div>

            <div class="col-1"> -->
               <div class="dropdown">
                    <a class="btn btn-outline-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                        Année
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <li><a class="dropdown-item" href="#">Par défaut - année en cours</a></li>
                        <li><a class="dropdown-item" href="#">2024-2025</a></li>
                        <li><a class="dropdown-item" href="#">2023-2024</a></li>
                        <li><a class="dropdown-item" href="#">Tout</a></li>
                    </ul>
                </div>
            </div>
        </div>
         <div id="searchResultPart"></div>
        
            <hr/>
            <div id='modalePart'></div>
    <div id='resultPart'></div>
    
    `;
  document.querySelector("#" + htlmPartId).innerHTML = outpuStr;

  try {
    /*** Actions */
    // *** Search action
    document.querySelector("#buttonSearch").onclick = async function () {
      // ** get search
      let personsList = await getPersonforCriteria(document.querySelector("#searchString").value);
      // *** Display search result
      let outpuStr = displayResultList(personsList);
      document.querySelector("#resultPart").innerHTML = outpuStr;

    };

    // document.querySelector("#brevoButton").onclick = async function () {
    //   await exportPersonListtoBrevoMVC("modalePart");
    // };

    // document.querySelector("#exportButton").onclick = async function () {
    //   await getExportPersonListViewDisplay("modalePart");
    // };

    // document.querySelector("#removePersonButton").onclick = function () {
    //   let chechedPersons = getCheckedPersonList();
    //   removeSubList(chechedPersons);
    //   displaySelectionjsContent("mainActiveSection");

    // };



  } catch (error) {
    document.querySelector("#messageSection").innerHTML = `</br><div class="alert alert-danger" style = "margin-top:30px" role = "alert" > ${error} - ${error.fileName}</br > ${error.stack}  </div > `;
  }
}

/**
 * 
 * @param {*} personsList 
 * @returns 
 */
function displayResultList(personsList) {
  let outpuStr = `</hr>
     <div class="col-12 "> ${personsList.length} résultats dans la liste
    </div >

    <table class="table table-striped">
    <thead>
        <tr>
            <th scope="col">Nom Prénom</th>
            <th scope="col">Email</th>
            <th scope="col">Téléphone</th>
            <th scope="col">Adhésions</th>
            <th scope="col">Activités</th>            
        </tr>
    </thead>
    <tbody>`;

  personsList.map((person, index) => {
    outpuStr += `<tr>         
      <td><a href="${getAppPath()}/src/person/person.html?per_id=${person.per_id}"> ${person.per_nom}  ${person.per_prenom}</a></td>
      <td><a href="${getAppPath()}/src/person/person.html?per_id=${person.per_id}">${person.per_email}</a></td> 
      <td><a href="${getAppPath()}/src/person/person.html?per_id=${person.per_id}"> ${person.per_tel}</a></td> 
      <td > ${person.subscrCOncat}</td> 
      <td>${person.inscrptCOncat}</td>                        
      </tr>`;
  });
  outpuStr += `</tbody>
    </table>
    `;
  return outpuStr;
}


