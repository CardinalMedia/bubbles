window.onload = function(){
  var signupForm   = document.querySelector('#mailchimp_signup');
  var confirmation = document.querySelector('#mailchimp_confirmation');
  var input        = signupForm.querySelector('[name="email"]');

  var confirmationMessage = function(el, message, toBeRemoved){
    el.innerHTML = message;
    if(toBeRemoved) toBeRemoved.remove();
  }

  signupForm.addEventListener('submit', function(e){
    e.preventDefault();

    if(!e.target.dataset.list){
      throw new Error('MailChimp list id not provided');
    }

    axios({
      method:  'post',
      url:     '/wp-json/bubbles/v1/submit',
      data:    JSON.stringify({
        email_address: input.value,
        list_id: e.target.dataset.list
      }),
      headers: {'Content-Type': 'application/json'}
    })
    .then(function(res){
      if(res.status === 200){
        confirmationMessage(confirmation, 'Thank you!', signupForm);
      } else {
        confirmationMessage(confirmation, 'Something went wrong');
      }
    }).catch(function(err){
      var body = JSON.parse(err.response.data.body);
      confirmationMessage(confirmation, body.title)
    });
  });
}
