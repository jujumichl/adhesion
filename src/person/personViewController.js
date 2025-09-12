// *** Component ressources
import { getPerson } from './personService.js'
import { headerViewDisplaySelectionjs, addMultipleEnventListener } from '../shared/functions.js'
/**
 * when called from the url
 * get the parameters and launch the controller
 */
export async function startPersonController() {

  // *** Initialisations
  try {
    let searchParams = new URLSearchParams(window.location.search);

    headerViewDisplaySelectionjs("menuSection")


    // *** Get url params and launch controller
    searchParams = new URLSearchParams(window.location.search);
    if (searchParams.has('per_id'))
      displayPersonContent("mainActiveSection", searchParams.get('per_id'));

  } catch (error) {
    document.querySelector("#messageSection").innerHTML = `<div class="alert alert-danger" style = "margin-top:30px" role = "alert" > ${error} - ${error.fileName}</br>${error.stack}  </div > `;
  }
}


/**
 * Display 
 * @param {*} htlmPartId 
 * @param {*} searchString : the string to searched in the database 
 */
export async function displayPersonContent(htlmPartId, per_id) {

  // *** Display the controller skeleton
  let initString = `
      <div class="row" style="margin-top:100px">
        <div class="col-12">
          <div class="d-flex justify-content-between" style="backgournd-color:">
              <div class="h5" style="color:#d07d29">Personne JS
              </div>

           <!--   <div class="d-flex justify-content-end" >
                <div id="removePersonButton" style="cursor: pointer;margin-right:2px" data-toggle="tooltip" data-placement="bottom" title="Créer une liste Brevo">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                  </svg>             
                </div>

                <div id="plusPersonButton" style="cursor: pointer;margin-right:10px" data-toggle="tooltip" data-placement="bottom" title="Créer une liste Brevo">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-clipboard-plus" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M8 7a.5.5 0 0 1 .5.5V9H10a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V10H6a.5.5 0 0 1 0-1h1.5V7.5A.5.5 0 0 1 8 7"/>
                  <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1z"/>
                  <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0z"/>
                  </svg>
              </div>
 
              <div class="" style="cursor: pointer;margin-left:10px" data-toggle="tooltip" data-placement="bottom" title="Créer une liste Brevo">
                <div id="brevoButton" style="cursor: pointer;margin-left:5px" data-toggle="tooltip" data-placement="bottom" title="Créer une liste Brevo">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-mailbox-flag" viewBox="0 0 16 16">
                      <path d="M10.5 8.5V3.707l.854-.853A.5.5 0 0 0 11.5 2.5v-2A.5.5 0 0 0 11 0H9.5a.5.5 0 0 0-.5.5v8zM5 7c0 .334-.164.264-.415.157C4.42 7.087 4.218 7 4 7s-.42.086-.585.157C3.164 7.264 3 7.334 3 7a1 1 0 0 1 2 0"/>
                      <path d="M4 3h4v1H6.646A4 4 0 0 1 8 7v6h7V7a3 3 0 0 0-3-3V3a4 4 0 0 1 4 4v6a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V7a4 4 0 0 1 4-4m0 1a3 3 0 0 0-3 3v6h6V7a3 3 0 0 0-3-3"/>
                  </svg>
                </div>
              </div>
              <div class="" style="cursor: pointer;margin-left:2px" data-toggle="tooltip" data-placement="bottom" title="Exporter la liste" >
                <div id="exportButton" style="cursor: pointer;margin-left:5px" data-toggle="tooltip" data-placement="bottom" title="Créer une liste Brevo">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-cloud-download" viewBox="0 0 16 16">
                          <path d="M4.406 1.342A5.53 5.53 0 0 1 8 0c2.69 0 4.923 2 5.166 4.579C14.758 4.804 16 6.137 16 7.773 16 9.569 14.502 11 12.687 11H10a.5.5 0 0 1 0-1h2.688C13.979 10 15 8.988 15 7.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 2.825 10.328 1 8 1a4.53 4.53 0 0 0-2.941 1.1c-.757.652-1.153 1.438-1.153 2.055v.448l-.445.049C2.064 4.805 1 5.952 1 7.318 1 8.785 2.23 10 3.781 10H6a.5.5 0 0 1 0 1H3.781C1.708 11 0 9.366 0 7.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383"/>
                          <path d="M7.646 15.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 14.293V5.5a.5.5 0 0 0-1 0v8.793l-2.146-2.147a.5.5 0 0 0-.708.708z"/>
                      </svg>
                  </div>
              </div> -->
              <!-- <div class="" style="cursor: pointer;margin-left:2px" data-toggle="tooltip" data-placement="bottom" title="Imprimer la liste">
                  <svg   xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
                      <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1"/>
                      <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1"/>
                      </svg>
              </div>-->
              
            </div>
        </div>
      </div>
            <hr/>
            <div id='personPart'></div>
    <div id='resultPart'></div>
    
    `;
  document.querySelector("#" + htlmPartId).innerHTML = initString;

  try {

    // *** Load data from API
    let output = '';

    document.querySelector("#" + htlmPartId).innerHTML = initString;

    // *** Get the person data
    let personhtml = await displayPerson(per_id);

    // *** Display the person data
    document.querySelector("#personPart").innerHTML = personhtml;


    /*** Actions */
    // document.querySelector("#plusPersonButton").onclick = function () {
    //   getpersonsViewDisplay("modalePart");
    //   let personListhtml = displayPersonList();
    //   document.querySelector("#resultPart").innerHTML = personListhtml;
    // };

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
    document.querySelector("#messageSection").innerHTML = `</br></br><div class="alert alert-danger" style = "margin-top:30px" role = "alert" > ${error} - ${error.fileName}</br > ${error.stack}  </div > `;
  }
}

/**
 * 
 * @param {*} per_id 
 * @returns 
 */
export async function displayPerson(per_id) {

  let person = await getPerson(per_id);
  let output = "";
  // output += `person :${per_id} -  ${person.per_nom}`;


  output += `
  <div style = "margin-top:20px">
    <div class="row">
   
      <div class="col-6">
        <div class="row align-items-center">
          <div class="col-2 mb-1">
            <label for="exampleFormControlInput1" class="col-form-label">Civilité
            </label>
          </div>
          <div class="col-8">
          <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" value="'.$person['civ_libelle'].'">
          </div>
        </div>

        <div class="row align-items-center">
          <div class="col-2 mb-1">
            <label for="exampleFormControlInput1" class="col-form-label">Nom
            </label>
          </div>
          <div class="col-8">
          <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" value="${person.per_nom}">
          </div>
        </div>

        <div class="row align-items-center">
          <div class="col-2 mb-1">
            <label for="exampleFormControlInput1" class="col-form-label">Prénom
            </label>
          </div>
          <div class="col-8 mb-1">
          <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" value="${person['per_prenom']}">
          </div>
        </div>

        <div class="row align-items-center">
          <div class="col-2 mb-1">
            <label for="exampleFormControlInput1" class="col-form-label">Email
            </label>
          </div>
          <div class="col-8">
          <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" value="${person['per_email']}">
          </div>
        </div>

        <div class="row align-items-center">
          <div class="col-2 ">
            <label for="exampleFormControlInput1" class="col-form-label">Téléphone
            </label>
          </div>
          <div class="col-8">
          <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" value="${person['per_tel']}">
          </div>
        </div>

        <div class="row align-items-center">
          <div class="col-2 ">
            <label for="exampleFormControlInput1" class="col-form-label">Date naissance
            </label>
          </div>
          <div class="col-8">
            <input type="date" class="form-control" id="exampleFormControlInput1" placeholder="" value="${person['per_dat_naissance']}">
          </div>
        </div>
        
      </div>
        
      <div class="col-6">
        <div class="row align-items-center">
          <div class="col-2 mb-1">
            <label for="exampleFormControlInput1" class="col-form-label">Adresse
            </label>
          </div>
          <div class="col-8">
            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" value="${person['per_adresse']}">
          </div>
        </div>

        <div class="row align-items-center">   
          <div class="col-2 mb-1">
            <label for="exampleFormControlInput1" class="col-form-label">Code postal
            </label>
          </div>
          <div class="col-8">
          <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" value="${person['per_code_postal']}">
          </div>
        </div>

        <div class="row align-items-center">   
          <div class="col-2 mb-1">
            <label for="exampleFormControlInput1" class="col-form-label">Ville
            </label>
          </div>
          <div class="col-8">
            <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" value="${person['per_ville']}">
          </div>
        </div>
      </div> 
      
    </div>
    
  ${displayPersonSubscriptions(person)}
  ${displayPersonPurchases(person)}
  ${displayPersonPayments(person)}


  </div>
  `;
  return output;
}

/**
 * 
 * @param {*} person 
 * @returns 
 */
function displayPersonSubscriptions(person) {
  let output = `
  <div class="row" style="margin-top:30px">
    <div class="col-12">
      <div class="d-flex justify-content-between" style="backgournd-color:">
        <div class="h6" style="color:#d07d29">Adhésions
        </div>
        <div class="d-flex justify-content-end" >
       <!--   <div class="" style="cursor: pointer;margin-left:5px" data-toggle="tooltip" data-placement="bottom" title="Ajoute adhésion">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-plus-square" viewBox="0 0 16 16">
              <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z" />
              <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
            </svg>
          </div> -->
        </div>
      </div>
    </div>

    <hr />
    <table class="table table-striped">
      <thead>
        <tr>
          <th scope="col">Saison</th>
          <th scope="col">Adhésion</th>
          <th scope="col">Date Adhésion</th>
          <th scope="col">Montant</th>
          <th scope="col">Date début</th>
          <th scope="col">Date fin</th>
          <th scope="col">Règlement</th>
        </tr>
      </thead>
      <tbody>
        `;
  if (person['subscriptions'].length > 0) {
    for (let subs = 0; subs < person['subscriptions'].length; ++subs) {
      output += `<tr>
        <td> ${person['subscriptions'][subs]['ans_libelle']}</td >
        <td>${person['subscriptions'][subs]['act_libelle']}</td>
        <td> ${person['subscriptions'][subs]['ins_date_inscription']}</td>
        <td>${person['subscriptions'][subs]['ins_montant']}€</td>
        <td> ${person['subscriptions'][subs]['ins_debut']}</td>
        <td> ${person['subscriptions'][subs]['ins_fin']}</td>
        <td> ${person['subscriptions'][subs]['reg_montant']}€ - ${person['subscriptions'][subs]['reg_date']} - ${person['subscriptions'][subs]['mreg_code']}</td>
    </tr > `;
    }
  } else {
    output += '<tr><td>Pas d\'adhésion pour cette personne</td></tr>';
  }
  output += `
  </tbody >
        </table >
      </div >
    `;

  return output;
}


/**
 * 
 * @param {*} person 
 * @returns 
 */
function displayPersonPurchases(person) {
  let output = `
  <div class="row" style="margin-top:30px">
    <div class="col-12">
      <div class="d-flex justify-content-between" style="backgournd-color:">
        <div class="h6" style="color:#d07d29">Activités
        </div>
        <div class="d-flex justify-content-end" >
          <!-- <div class="" style="cursor: pointer;margin-left:5px" data-toggle="tooltip" data-placement="bottom" title="Ajoute adhésion">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-plus-square" viewBox="0 0 16 16">
              <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z" />
              <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
            </svg>
          </div> -->
        </div>
      </div>
    </div>

    <hr />
    <table class="table table-striped">
      <thead>
        <tr>
               <th scope="col">Saison</th>
                <th scope="col">Date inscription</th>
                <th scope="col">Activité</th>
                <th scope="col">Montant</th>
                <th scope="col">Date début</th>
                <th scope="col">Date fin</th>
                <th scope="col">Règlement</th>
        </tr>
      </thead>
      <tbody>
        `;
  if (person['purchases'].length > 0) {
    for (let subs = 0; subs < person['purchases'].length; ++subs) {
      output += `<tr>
        <td> ${person['purchases'][subs]['ans_libelle']}</td >
        <td> ${person['purchases'][subs]['ins_date_inscription']}</td>
        <td>${person['purchases'][subs]['act_libelle']}</td>
        <td>${person['purchases'][subs]['ins_montant']}€</td>
        <td> ${person['purchases'][subs]['ins_debut']}</td>
        <td> ${person['purchases'][subs]['ins_fin']}</td>
        <td> ${person['purchases'][subs]['reg_montant']}€ - ${person['purchases'][subs]['reg_date']} - ${person['purchases'][subs]['mreg_code']}</td>
    </tr > `;
    }
  } else {
    [subs]
    output += '<tr><td>Pas d\'activité pour cette personne</td></tr>';
  }
  output += `
  </tbody >
        </table >
      </div >
    `;

  return output;
}




/**
 * 
 * @param {*} person 
 * @returns 
 */
function displayPersonPayments(person) {
  let output = `
  <div class="row" style="margin-top:30px">
    <div class="col-12">
      <div class="d-flex justify-content-between" style="backgournd-color:">
        <div class="h6" style="color:#d07d29">Règlements
        </div>
        <div class="d-flex justify-content-end" >
         <!--  <div class="" style="cursor: pointer;margin-left:5px" data-toggle="tooltip" data-placement="bottom" title="Ajoute adhésion">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-plus-square" viewBox="0 0 16 16">
              <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z" />
              <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
            </svg> -->
          </div>
        </div>
      </div>
    </div>

    <hr />
    <table class="table table-striped">
      <thead>
        <tr>
            <th scope="col">Date règlement</th>
            <th scope="col">Montant règlement</th>
            <th scope="col">Mode règlement</th>
            <th scope="col">Détail</th>
        </tr>
      </thead>
      <tbody>
        `;
  if (person['payments'].length > 0) {
    for (let subs = 0; subs < person['payments'].length; ++subs) {
      output += `<tr>
        <td> ${person['payments'][subs]['reg_date']}</td >
         <td>${person['payments'][subs]['reg_montant']}€</td>
        <td> ${person['payments'][subs]['mreg_code']}</td>
        <td> ${person['payments'][subs]['reg_details']}</td>
    </tr > `;
    }
  } else {
    [subs]
    output += '<tr><td>Pas de paiement pour cette personne</td></tr>';
  }
  output += `
  </tbody >
        </table >
      </div >
    `;

  return output;
}