import { getAppPath } from '../shared/functions.js'

export async function getPerson(pers_id) {

    // TODO : tester la validité des paramètres

    let wsUrl = `${getAppPath()}/src/api/index.php/person/${pers_id}`;
    let responsefr = await fetch(wsUrl, {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
        }
    });
    if (responsefr.ok) {
        // *** Get the data and save in the sessionStorage
        const data = await responsefr.json();
        sessionStorage.setItem("person", JSON.stringify(data));
        console.log("getPerson  await ok ");
        return (data);

    } else {
        console.log(`getPerson Error: ${JSON.stringify(responsefr)
            } `);
        throw new Error("getPerson Error message : " + responsefr.status + " " + responsefr.statusText);
    }
}



export async function getActivities() {

    // TODO : tester la validité des paramètres

    let wsUrl = `${getAppPath()}/src/api/index.php/activities`;
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