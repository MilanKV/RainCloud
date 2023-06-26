const signup = {

    uploading: false,

    submit:function(e)
    {
        e.preventDefault();

        if(signup.uploading)
        {
            alert("Please wait...");
            return;
        }

        let button = document.querySelector(".sign-button");
        button.innerHTML = `Saving...`;

        let myform = new FormData();
		myform.append('data_type','user_signup');

        // Get all inputs
        let inputs = e.currentTarget.querySelectorAll("input");
        let hasEmptyField = false;

        for (var i = 0; i < inputs.length; i++) 
        {
            let input = inputs[i];

            // Check if the input is required and empty
            if (input.hasAttribute('required') && input.value.trim() === '') 
            {
                hasEmptyField = true;
                let errorMessage = document.querySelector('.error-' + input.name);
                if(errorMessage) 
                {
                    errorMessage.innerHTML = 'This field is required.'; // Display error message
                    errorMessage.classList.remove('hidden');
                }
            }

            myform.append(input.name, input.value.trim());
        }

        if (hasEmptyField) 
        {
            // At least one required field is empty, prevent form submission
            button.innerHTML = `SIGNUP`;
            return;
        }

        signup.uploading = true;

        let xm = new XMLHttpRequest();
        xm.addEventListener('readystatechange', function() 
        {
            if(xm.readyState == 4)
            {
                signup.uploading = false;
                button.innerHTML = `SIGNUP`;

                if(xm.status == 200)
                {
                    // console.log(xm.responseText);

                    let obj = JSON.parse(xm.responseText);
                    
                    if(obj.success && obj.data_type == "user_signup")
                    {
                        alert("Your account was created!");
                        window.location = '../../view/auth/login.php';
                        console.log(window.location);

                    } else {
                        
                        // Empty old error messages
                        let errors = document.querySelectorAll(".error-message");

                        for (let i = 0; i < errors.length; i++) 
                        {
                            errors[i].innerHTML = "";
                            errors[i].classList.add("hidden")
                        }

                        // Show new Errors
                        for(key in obj.errors)
                        {
                            let item = document.querySelector(".error-" + key);
                            if(item) 
                            {
                                item.innerHTML = obj.errors[key];
                                item.classList.remove("hidden");
                            }
                        }
                    }

                } else {
                    console.log(xm.responseText);
                }
            }    
        });

        // Open a POST request api.php and send the FormData
        xm.open('post', '../../api.php', true);
        xm.send(myform);
    },
}

const login = {

    uploading: false,

    submit:function(e)
    {
        e.preventDefault();

        if(login.uploading)
        {
            alert("Please wait...");
            return;
        }

        let button = document.querySelector(".log-button");
        button.innerHTML = `Logging in...`;

        let myform = new FormData(e.currentTarget);
		myform.append('data_type','user_login');

        // Clear existing error messages
        let errorMessages = document.querySelectorAll(".error-message");
        errorMessages.forEach(function (errorMessage) 
        {
            errorMessage.textContent = "";
            errorMessage.classList.add("hidden");
        });

        login.uploading = true;

        let xm = new XMLHttpRequest();
        xm.addEventListener('readystatechange', function() 
        {
            if(xm.readyState == 4)
            {
                login.uploading = false;
                button.innerHTML = `LOGIN`;

                if(xm.status == 200)
                {
                    // console.log(xm.responseText);

                    let obj = JSON.parse(xm.responseText);
                    
                    if(obj.success && obj.data_type == "user_login")
                    {
                        alert("Login successfuly");
                        window.location = '../../view/layout.php';

                    } else {
                        
                        if (obj.errors) 
                        {
                            for (let key in obj.errors) 
                            {
                                let errorMessage = document.querySelector(".error-" + key);
                                if (errorMessage) 
                                {
                                    errorMessage.textContent = obj.errors[key];
                                    errorMessage.classList.remove("hidden");
                                }
                            }
                        } else {
                            let errorContainer = document.querySelector(".error-container");
                            errorContainer.innerHTML = "An error occurred. Please try again later.";
                            errorContainer.classList.remove("hidden");
                        }
                    }
                } else {
                    console.log(xm.responseText);
                }
            }    
        });

        // Open a POST request api.php and send the FormData
        xm.open('post', '../../api.php', true);
        xm.send(myform);
    },
}