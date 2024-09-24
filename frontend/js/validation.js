
function validateEmail(form) {
    let email = form.email.value;
    let isValid = true;
    const regEmail = new RegExp(/^[A-Za-z0-9_!#$%&'*+\/=?`{|}~^.-]+@[A-Za-z0-9.-]+$/, "gm");
    if(!regEmail.test(email)){
        flash("Please enter a valid e-mail address.", "danger");
        isValid = false;
    }
    // Validate email
    if (email.trim() === "") {
        flash("Email is required.", "danger");
        isValid = false;
    }
    return isValid;
}

// Validate username
function validateUsername(form) {
    let username = form.username.value;
    let isValid = true;
    const regUsername = new RegExp(/^[a-z0-9_-]{3,16}$/);
    let validUsername = username.match(re);
    if (validUsername == null) {
        flash("Invalid username.  Please try again.");
        isValid = false;
    }
    if (username.trim() === "") {
        flash("Username is required.", "danger");
        isValid = false;
    }
    return isValid;
}

// Validate password
function validatePassword(form) {
    let pw = form.password.value;
    let con = form.confirm.value;
    let isValid = true;
    if (pw !== con) {
        flash("Password and Confirm Password must match.", "danger");
        isValid = false;
    }
    if (pw.length < 8) {
        flash("Password must be at least 8 characters long.", "danger");
        isValid = false;
    }
    return isValid;
}

function validateForm(form) {
    console.log("Validating form...");

    validateEmail(form);
    validateUsername(form);
    validatePassword(form);
}
