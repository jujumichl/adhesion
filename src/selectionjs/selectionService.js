
export async function getPersonforCriteria(searchCriteria) {

    // TODO : tester la validité des paramètres

    let wsUrl = window.location.protocol + '//' + window.location.hostname + '/adhesion/src/api/index.php/searchperson/' + searchCriteria;
    // http://localhost/adhesion/src/api/index.php/searchperson/bou
    let responsefr = await fetch(wsUrl, {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
        }
    });
    if (responsefr.ok) {
        // *** Get the data and save in the sessionStorage
        const data = await responsefr.json();
        sessionStorage.setItem("personsSubList", JSON.stringify(data));
        console.log("getPersonforCriteria  await ok ");
        return (data);

    } else {
        console.log(`getPersonforCriteria Error: ${JSON.stringify(responsefr)} `);
        throw new Error("getPersonforCriteria Error message : " + responsefr.status + " " + responsefr.statusText);
    }
}


export async function getPersonforActivity(act_id) {

    // TODO : tester la validité des paramètres

    let wsUrl = window.location.protocol + '//' + window.location.hostname + '/adhesion/src/api/index.php/searchpersonbyactivity/' + act_id;
    // http://localhost/adhesion/src/api/index.php/searchperson/bou
    let responsefr = await fetch(wsUrl, {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
        }
    });
    if (responsefr.ok) {
        // *** Get the data and save in the sessionStorage
        const data = await responsefr.json();
        sessionStorage.setItem("personsSubList", JSON.stringify(data));
        console.log("getPersonforCriteria  await ok ");
        return (data);

    } else {
        console.log(`getPersonforCriteria Error: ${JSON.stringify(responsefr)} `);
        throw new Error("getPersonforCriteria Error message : " + responsefr.status + " " + responsefr.statusText);
    }
}


export async function getActivities() {

    // TODO : tester la validité des paramètres

    let wsUrl = window.location.protocol + '//' + window.location.hostname + '/adhesion/src/api/index.php/activities';
    // http://localhost/adhesion/src/api/index.php/searchperson/bou
    let responsefr = await fetch(wsUrl, {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
        }
    });
    if (responsefr.ok) {
        // *** Get the data and save in the sessionStorage
        const data = await responsefr.json();
        sessionStorage.setItem("activities", JSON.stringify(data));
        console.log("getPersonforCriteria  await ok ");
        return (data);

    } else {
        console.log(`getPersonforCriteria Error: ${JSON.stringify(responsefr)} `);
        throw new Error("getPersonforCriteria Error message : " + responsefr.status + " " + responsefr.statusText);
    }
}

/**
 * Get the current persons list
 * @returns 
 */
export function getList() {
    let personList = null;
    // sessionStorage.setItem("personList", JSON.stringify(data));
    let personListJson = sessionStorage.getItem("personList");
    if (personListJson)
        personList = JSON.parse(personListJson);
    else
        personList = [];

    return personList;

}
/**
 * Add a list of persons of the current list
 * @param {*} newPersons 
 */
export function addList(newPersons) {
    let newPersonList = null;
    let personListJSon = sessionStorage.getItem("personList");
    if (personListJSon) {
        let currentPersonList = JSON.parse(personListJSon);
        newPersonList = currentPersonList.concat(newPersons);
        sessionStorage.setItem("personList", JSON.stringify(newPersonList));
    } else {
        sessionStorage.setItem("personList", JSON.stringify(newPersons));
    }
}
export function getSubList() {
    let personList = null;
    // sessionStorage.setItem("personSubList", JSON.stringify(data));
    let personListJson = sessionStorage.getItem("personsSubList");
    if (personListJson)
        personList = JSON.parse(personListJson);
    else
        personList = [];

    return personList;

}