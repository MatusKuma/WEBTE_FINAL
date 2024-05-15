function validateIsEmpty(message, inputID, errorOutputID){
    let inputField = document.getElementById(inputID);

    if (!inputField.value){
        inputField.style.border = "3px solid red";
        showError(message, errorOutputID);
        return true;
    }
    else {
        hideError(errorOutputID)
        return false;
    }
}

function hideError (errorOutputID) {
    let error = document.getElementById(errorOutputID);
    error.style.visibility = "hidden";
    error.innerHTML = "";
}

function showError(message, errorOutputID) {
    let error = document.getElementById(errorOutputID);
    error.style.visibility = "visible";
    error.style.color = "red";
    error.innerHTML = message
}

function validateInput(empty_message, validity_message, inputID, errorOutputID){
    let input = document.getElementById(inputID);

    if(validateIsEmpty(empty_message, inputID, errorOutputID)){
        input.style.border = "3px solid red";
        return false;
    }
    
    if (!input.checkValidity()){
        showError(validity_message, errorOutputID);
        input.style.border = "3px solid red";
        return false;
    }
    else {
        input.style.border = "3px solid green";
        hideError(errorOutputID);
        return true;
    }
    
}


function validateInputNotRequired(validity_message, inputID, errorOutputID){
    let input = document.getElementById(inputID);
    
    if (!input.checkValidity()){
        showError(validity_message, errorOutputID);
        input.style.border = "3px solid red";
        return false;
    }
    else {
        input.style.border = "3px solid green";
        hideError(errorOutputID);
        return true;
    }
    
}

function validateForm(){
    if(!validateInput('Please enter Username!', 'Username must have between 6-32 chars', 'username', 'error-username') ||
    !validateInput('Please enter Password!', 'Password must have between 8-100 chars', 'password', 'error-password')
    ){
        return false;
    }
    return true;
}





