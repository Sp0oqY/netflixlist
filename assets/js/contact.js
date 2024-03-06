// Get data
const nameInput = document.querySelector("#name");
const email = document.querySelector("#email");
const subject = document.querySelector("#subject");
const message = document.querySelector("#message");
const success = document.querySelector("#success");
const errorNodes = document.querySelectorAll(".error");

// Validate data
function validateFrom()
{
    clearMessages();
    let errorFlag = false;

    if(nameInput.value.length < 1)
    {
        errorNodes[0].innerText = "Name cannot be blank";
        nameInput.classList.add("error-border")
        errorFlag = true;
    }

    if(!emailIsValid(email.value) && email.value.length > 1)
    {
        errorNodes[1].innerText = "Email is not valid";
        email.classList.add("error-border");
        errorFlag = true;
    }

    if(email.value.length < 1)
    {
        errorNodes[1].innerText = "Email cannot be blank";
        email.classList.add("error-border");
        errorFlag = true;
    }

    if(subject.value.length < 1)
    {
        errorNodes[2].innerText = "Subject cannot be blank";
        subject.classList.add("error-border");
        errorFlag = true;
    }

    if(message.value.length < 1)
    {
        errorNodes[3].innerText = "Please enter message";
        message.classList.add("error-border");
        errorFlag = true;
    }

    if(!errorFlag)
    {
        success.innerText = "YOUR FORM HAS BEEN SENT SUCCESSFULLY!";
    }
}

//Clear error / success messages
function clearMessages()
{
    for(let i = 0; i < errorNodes.length; i++)
    {
        errorNodes[i].innerText = "";
    }
    success.innerText = "";
    nameInput.classList.remove("error-border");
    email.classList.remove("error-border");
    subject.classList.remove("error-border");
    message.classList.remove("error-border");
}

//Check if email is valid
function emailIsValid(email)
{
    let pattern = /\S+@\S+\.\S+/;
    return pattern.test(email);
}