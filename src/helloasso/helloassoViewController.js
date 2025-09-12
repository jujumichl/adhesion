// *** Component ressources
import { getAccessToken, getHelloassoForms, getHelloassoFormOrders } from './helloassoService.js'
import { headerViewDisplaySelectionjs, addMultipleEnventListener } from '../shared/functions.js'
/**
 * when called from the url
 * get the parameters and launch the controller
 */
export async function startHelloassoController() {

  // *** Initialisations
  try {
    //   let searchParams = new URLSearchParams(window.location.search);

    headerViewDisplaySelectionjs("menuSection")


    // *** Get url params and launch controller
    //     searchParams = new URLSearchParams(window.location.search);
    //    if (searchParams.has('per_id'))
    displayHelloassoContent("mainActiveSection");

  } catch (error) {
    document.querySelector("#messageSection").innerHTML = `<div class="alert alert-danger" style = "margin-top:30px" role = "alert" > ${error} - ${error.fileName}</br>${error.stack}  </div > `;
  }
}


/**
 * Display 
 * @param {*} htlmPartId 
 * @param {*} searchString : the string to searched in the database 
 */
export async function displayHelloassoContent(htlmPartId, per_id) {

  // *** Display the controller skeleton
  let initString = `
      <div class="row" style="margin-top:100px">
        <div class="col-12">
          <div class="d-flex justify-content-between" style="backgournd-color:">
              <div class="h5" style="color:#d07d29">Intégration HelloAsso
              </div>
            
            </div>
        </div>
      </div>
            <hr/>
            <div id='status'></div>
    <div id='resultPart'></div>
    <div id='resultPartForm'></div>

    <div id='resultPartFormexplain'></div>

    
    `;
  document.querySelector("#" + htlmPartId).innerHTML = initString;

  try {

    // *** Load data from API
    let output = '';

    //    document.querySelector("#" + htlmPartId).innerHTML = initString;

    // *** Get the person data
    let accesstoken = await getAccessToken();
    // document.querySelector("#status").innerHTML = "Access token found : " + accesstoken;


    let forms = await getHelloassoForms(JSON.parse(accesstoken).access_token);
    let formsListHtml = displayFormsList(forms);

    document.querySelector("#resultPart").innerHTML = formsListHtml;


    document.querySelector("#formChoice")
      .addEventListener('click', async (event) => {
        // Check if the clicked element is an <li>
        if (event.target.tagName === 'LI') {
          // document.querySelector("#resultPartForm").innerHTML = event.target.id;
          let formType = event.target.getAttribute("slugType");;

          let formOrders = await getHelloassoFormOrders(JSON.parse(accesstoken).access_token, formType, event.target.id)
          document.querySelector("#resultPartForm").innerHTML = displayFormOrders(formOrders);

          document.querySelector("#resultPartFormexplain").innerHTML = displayFormOrdersFullFormat(formOrders);
        }
      });


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
 * @param {*} forms 
 * @returns
 * Manage nb of forms > pageSize 
 */
function displayFormsList(forms) {
  let outputStr = `
    <div class="col-6" style = "margin-right:5px" >
      <div class="dropdown" >
        <a class="btn btn-outline-secondary dropdown-toggle" href="#" role="button" id="activiteList" data-bs-toggle="dropdown" aria-expanded="false">
          Formulaires Helloasso
        </a>

        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink" id="formChoice">
          `;
  forms.map((form, index) => {
    outputStr += `<li class="dropdown-item" id="${form.formSlug}" slugType="${form.formType}">${form.formType} - ${form.title}</li>`
  });

  outputStr += `</ul>
      </div>

</div > `;


  return outputStr;
}


/**
 * 
 * @param {*} forms 
 * @returns
 * Manage nb of forms > pageSize 
 * TODO : Payments[0] 
 */
function displayFormOrders(formOrders) {
  let outputStr = `
    <div class="col-12" style = "margin-right:5px" >
  
        <ul class="" aria-labelledby="" id="">
          `;
  formOrders.map((formOrder, index) => {
    if (formOrder.order.formType == "Membership") {
      outputStr += `<li class="" id="${formOrder.order.id}">
      Adhésion : ${formOrder.order.formName} </br>
      Adhérent : ${formOrder.payer.email} - ${formOrder.payer.firstName} ${formOrder.payer.lastName} </br>
      Commande :  ${formOrder.order.id} - ${formOrder.order.date} </br>
      Paiement : ${(formOrder.payments) ? formOrder.payments[0].date - formOrder.payments[0].amount / 100 - formOrder.payments[0].state + "</br>" : "No payment"}
       
      Activités : ${(formOrder.options) ? formOrder.options.map((optionThis, index) => optionThis.name + " - " + optionThis.amount / 100 + "€") + "</br>" : " Pas d'activité"}
      <hr/></li>`
    } else if (formOrder.order.formType == "Event") {
      outputStr += `<li class="" id="${formOrder.order.id}">
      Event : ${formOrder.order.formName}</br>
      Adhérent : ${formOrder.payer.email} - ${formOrder.payer.firstName} ${formOrder.payer.lastName} </br>
      Commande :  ${formOrder.order.id} - ${formOrder.order.date} </br>
      Paiement : ${(formOrder.payments) ? formOrder.payments[0].amount - + "</br>" : "No payment</br>"}
       
      Activités : ${(formOrder.options) ? formOrder.options.map((optionThis, index) => optionThis.name + " - " + optionThis.amount / 100 + "€") + "</br>" : " Pas d'activité"}
      <hr/></li>`

    } else {
      outputStr += `<li class="" id="${formOrder.order.id}">
      Unkowned FormType 
      <hr/></li>`


    }


  })

  outputStr += `</ul>
      </div>
 `;

  return outputStr;
}

/**
 * 
 * @param {*} forms 
 * @returns
 * Manage nb of forms > pageSize 
 * TODO : Payments[0] 
 */
function displayFormOrdersFullFormat(formOrders) {
  let outputStr = `
    <div class="col-12" style = "margin-right:5px" >
  
        <ul class="" aria-labelledby="" id="">
          `;
  formOrders.map((formOrder, index) => {
    outputStr += `<li class="" id="${formOrder.order.id}">
         ${formOrder.order.id} : ${JSON.stringify(formOrder)}
    <hr/></li>`
  });

  outputStr += `</ul>
      </div>
 `;

  return outputStr;
}