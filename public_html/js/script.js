$(document).ready(function(){

  $("[data-href]").each(function(){
    $(this).data({
      "data-href": $(this).attr("data-href")
    }).removeAttr("data-href").addClass("is-link").click(function(){
      window.location.href = $(this).data("data-href");
    });
  });

  $("#add_card").submit(function(event){
    console.log("A");
    event.preventDefault();

    if(!$(this).find('select[name="cardexpiry_month"]').val() || !$(this).find('select[name="cardexpiry_year"]').val()) return;
    $("#add_card").find(":input:visible").attr("disabled", "disabled");
    $("#add_card").find("button[type=\"submit\"]").text("Loading...");

    console.log("B");

    Stripe.card.createToken({
      name: $(this).find('input[name="cardname"]').val(),
      number: $(this).find('input[name="creditcard"]').val(),
      cvc: $(this).find('input[name="cvv"]').val(),
      exp_month: $(this).find('select[name="cardexpiry_month"]').val(),
      exp_year: $(this).find('select[name="cardexpiry_year"]').val()
    }, function(cb_a, cb_b){
      console.log("C", cb_a, cb_b);
      $("#add_card").find("button[type=\"submit\"]").text("Saving...");

      $.ajax({
        url: "/card",
        method: "POST",
        data: {
          "action": "save",
          "password": $("#login_password").val(),
          "token": cb_b["id"]
        },
        dataType: "json",
        success: function(return_data){
          if(return_data["status"] == "error") {
            $("#add_card").find(":input:visible").removeAttr("disabled");
            $("#add_card_alert").text(return_data["message"]).show();
          }
          else window.location.href = "/settings?msg=card";
        },
        error: function(){
          alert("Oops an error occurred. Please try again, or contact support if the problem persists.");
          document.location.reload();
        }
      });
    });
  });
});
