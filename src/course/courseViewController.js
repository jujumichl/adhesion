// *** Component ressources
import { getActivities, getPersonsforActivity, getMoocPersonsforActivity, getActivity } from './courseService.js'
import { headerViewDisplaySelectionjs, getAppPath, getappRelease } from '../shared/functions.js'
/**
 * when called from the url
 * get the parameters and launch the controller
 */
export async function startCourseController() {



  // *** Initialisations
  try {

  } catch (error) {
    document.querySelector("#messageSection").innerHTML = `<div class="alert alert-danger" style = "margin-top:30px" role = "alert" > ${error} - ${error.fileName}</br>${error.stack}  </div > `;
  }

  headerViewDisplaySelectionjs("menuSection")
  displayCourseContent("mainActiveSection");
}

/**
 * Display 
 * @param {*} htlmPartId 
 * @param {*} searchString : the string to searched in the database 
 */
export async function displayCourseContent(htlmPartId) {

  let activities = await getActivities();

  // *** Display the controller skeleton
  let outpuStr = `
    <!--< h1 class="modal-title fs-5" id = "exampleModalLabel" > Listes des personnes</h1 > -->
        <div class="row" style="margin-top:100px">
            <div class="h5" style="color:#d07d29">Course JS</div>
            <hr/>

          <!--  <div class="col-4">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="" id="searchString" name="searchString" aria-label="" aria-describedby="" value="">
                    <button type="" value=""  class="btn btn-outline-secondary" id="buttonSearch">Chercher</button>
                </div>
            </div>
          -->
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
     
        </div>
         <div id="searchResultPart"></div>
        
   
    <div id='resultPart'></div>
    <div id='resultPartMooc'></div>
    `;
  document.querySelector("#" + htlmPartId).innerHTML = outpuStr;

  try {
    /*** Actions */

    document.querySelector("#activityChoice")
      .addEventListener('click', async (event) => {
        // Check if the clicked element is an <li>
        if (event.target.tagName === 'LI') {
          // *** Get the persons list for the activity 
          let personList = await getPersonsforActivity(event.target.id);

          let activity = await getActivity(event.target.id);
          // *** Get the mooc persons list
          let moocPersonList = await getMoocPersonsforActivity(activity.act_mooc_id);

          let moocPersonListAdded = addMoocDataToPersonlist(personList, moocPersonList);

          let personListhtml = displayPersonsResultList(moocPersonListAdded);
          document.querySelector("#resultPart").innerHTML = personListhtml;

          let moocpersonListhtml = displayMoocResultList(moocPersonList);
          document.querySelector("#resultPartMooc").innerHTML = moocpersonListhtml;
        }
      });

  } catch (error) {
    document.querySelector("#messageSection").innerHTML = `</br><div class="alert alert-danger" style = "margin-top:30px" role = "alert" > ${error} - ${error.fileName}</br > ${error.stack}  </div > `;
  }
}

/**
 * Add MOOC data to the persons list
 * @param {*} personsList 
 * @param {*} moocPersonsList 
 * @returns 
 */
function addMoocDataToPersonlist(personsList, moocPersonsList) {

  let personListBis = JSON.parse(JSON.stringify(personsList));
  if (moocPersonsList.length > 0) {
    personListBis.map((person, index) => {

      let findMoocPersonindex = moocPersonsList.findIndex((moocPersonList) => moocPersonList.email == person.per_email)
      //  console.log("REcherche : " + findMoocPersonindex + "</br>");
      if (findMoocPersonindex > -1) {
        person.moccData = moocPersonsList[findMoocPersonindex].email;
        // +
        //   " -" + findMoocPersonindex + moocPersonsList[findMoocPersonindex].firstname + " -"
        // " -" + findMoocPersonindex + moocPersonsList[findMoocPersonindex].lastname;
        moocPersonsList[findMoocPersonindex].id = -1;
      }
    });
  }

  return personListBis;
}

/**
 * 
 * @param {*} personsList 
 * @returns 
 */
function displayPersonsResultList(personsList) {
  let outpuStr = `
     <div class="col-12 ">
    <hr/>
     <div class="h6" style="color:#d07d29"> Personnes inscrites à l'atelier</div>
     </div >
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
             <th scope="col">MOOC</th>          
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
      <td>${person.moccData ? person.moccData : 'pas Mooc'}</td> 
                             
      </tr>`;
  });
  outpuStr += `</tbody>
    </table>
    `;
  return outpuStr;
}


/**
 * 
 * @param {*} personsList 
 * @returns 
 */
function displayMoocResultList(moocPersonsList) {
  let outpuStr = `</hr>
     <div class="col-12 ">
    <hr/>
     <div class="h6" style="color:#d07d29"> Personnes présentes dans le MOOC et pas présentes dans DYB</div>
     </div >`;

  if (moocPersonsList.length > 0) {
    outpuStr += `<table class="table table-striped">
      <thead>
        <tr>
          <th scope="col">Nom Prénom</th>
          <th scope="col">Email</th>

        </tr>
      </thead>
      <tbody>`;

    moocPersonsList.map((moocPerson, index) => {
      if (moocPerson.id > -1) {
        outpuStr += `<tr>          
      <td>  ${moocPerson.firstname} ${moocPerson.lastname}</td>
        <td>${moocPerson.email}</td> 
        <td>${getMoocPersonroleDescription(moocPerson)}</td> 
        </tr>`;
      }
    });

    outpuStr += `</tbody>
    </table>
    `;
  } else {
    outpuStr += `</hr>
     <div class="col-12 "> Cet atelier n'est disponible dans le MOOC. 
    </div >`;
  }

  return outpuStr;
}


function getMoocPersonroleDescription(moocPerson) {
  let outpuStr = "";

  moocPerson.roles.map((role, index) => {
    outpuStr += role.shortname + ", ";

  });





  return outpuStr
}
