import { getAppPath } from '../shared/functions.js'

export async function getAccessToken() {

    const myHeaders = new Headers();
    myHeaders.append("Content-Type", "application/x-www-form-urlencoded");
    myHeaders.append("Cookie", "__cf_bm=kWM_c5GMpZ70C1i6lomZfL6xl49S5RcMduZ3_AC11mM-1757683808-1.0.1.1-3oGsDnfXVKH5AjhDqaTgTmN3ovyQtiGEJsgwOZT38UFyJ3TZkw6NqdblVlGcIvJHL9ZVx3.9_yXdM0EzIQhCMZAT4WHhqVw1LZmIdNQIC0M");

    const urlencoded = new URLSearchParams();
    urlencoded.append("grant_type", "client_credentials");
    urlencoded.append("client_id", "24064197e198437f92759cc82c4d9f04");
    urlencoded.append("client_secret", "DvhBYv6E83nPV5piEmRGNxm93jL9A2R4");

    const requestOptions = {
        method: "POST",
        headers: myHeaders,
        body: urlencoded,
        redirect: "follow"
    };

    // fetch("https://api.helloasso.com/oauth2/token", requestOptions)
    //     .then((response) => response.text())
    //     .then((result) => console.log(result))
    //     .catch((error) => console.error(error));

    // TODO : tester la validité des paramètres


    let responsefr = await fetch("https://api.helloasso.com/oauth2/token", requestOptions);

    if (responsefr.ok) {
        // *** Get the data and save in the sessionStorage
        const data = await responsefr.text();
        sessionStorage.setItem("accesstoken", JSON.stringify(data));
        console.log("getAccessToken  await ok ");
        return (data);

    } else {
        console.log(`getAccessToken Error: ${JSON.stringify(responsefr)
            } `);
        throw new Error("getAccessToken Error message : " + responsefr.status + " " + responsefr.statusText);
    }
}


/**
 * 
 * @param {*} access_token 
 * @returns 
 * 
 * TODO : manage number of forms (check that totalCount is < pageSize         "pageSize": 20, "totalCount": 68,)
 */
export async function getHelloassoForms(access_token) {

    const myHeaders = new Headers();
    myHeaders.append("Accept", "application/json");
    myHeaders.append("Authorization", "Bearer " + access_token);
    myHeaders.append("Cookie", "__cf_bm=ADvA2X_xRbnoWE8uOMWUZaIRxz8VtwSKv5IXUXFMmrw-1757689769-1.0.1.1-p8UXbythXR6wqLwq0a0Nt0tfnLjPHsO7CZ0Txg1_pKaig0H5n1RjlFj1c85LwK07hp8z69SprhpoCCcMRfHxv8V_Nf2VELUwYYwYkI4qyUo");

    const requestOptions = {
        method: "GET",
        headers: myHeaders,
        redirect: "follow"
    };

    let responsefr = await fetch("https://api.helloasso.com/v5/organizations/cercle-celtique-de-rennes/forms?states=public&pageSize=100", requestOptions);

    if (responsefr.ok) {
        // *** Get the data and save in the sessionStorage
        const data = await responsefr.json();
        sessionStorage.setItem("forms", JSON.stringify(data.data));
        console.log("getHelloassoForms  await ok ");
        return (data.data);

    } else {
        console.log(`getHelloassoForms Error: ${JSON.stringify(responsefr)
            } `);
        throw new Error("getHelloassoForms Error message : " + responsefr.status + " " + responsefr.statusText);
    }
}



/**
 * 
 * @param {*} access_token 
 * @returns 
 * 
 * TODO : manage number of forms (check that totalCount is < pageSize         "pageSize": 20, "totalCount": 68,)
 */
export async function getHelloassoFormOrders(access_token, formType, formSlug) {

    const myHeaders = new Headers();
    myHeaders.append("Accept", "application/json");
    myHeaders.append("Authorization", "Bearer " + access_token);
    myHeaders.append("Cookie", "__cf_bm=ADvA2X_xRbnoWE8uOMWUZaIRxz8VtwSKv5IXUXFMmrw-1757689769-1.0.1.1-p8UXbythXR6wqLwq0a0Nt0tfnLjPHsO7CZ0Txg1_pKaig0H5n1RjlFj1c85LwK07hp8z69SprhpoCCcMRfHxv8V_Nf2VELUwYYwYkI4qyUo");

    const requestOptions = {
        method: "GET",
        headers: myHeaders,
        redirect: "follow"
    };

    let responsefr = await fetch(`https://api.helloasso.com/v5/organizations/cercle-celtique-de-rennes/forms/${formType}/${formSlug}/items?withCount=true&withDetails=true`, requestOptions);

    if (responsefr.ok) {
        // *** Get the data and save in the sessionStorage
        const data = await responsefr.json();
        sessionStorage.setItem("orders", JSON.stringify(data.data));
        console.log("getHelloassoFormOrders  await ok ");
        return (data.data);

    } else {
        console.log(`getHelloassoFormOrders Error: ${JSON.stringify(responsefr)
            } `);
        throw new Error("getHelloassoFormOrders Error message : " + responsefr.status + " " + responsefr.statusText);
    }
}