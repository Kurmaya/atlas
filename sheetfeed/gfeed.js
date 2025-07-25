jQuery(document).ready(function () {
  let locations = {
    "": "Open this select menu",
    HSR: "HSR",
    WHITEFIELD: "Whitefield",
    "INDRA NAGAR": "Indira Nagar",
    CHENNAI: "Chennai",
    HYDERABAD: "Hyderabad",
  };
  let location_field = $("#location");
  location_field.empty();
  $.each(locations, function (index, item) {
    location_field.append($("<option>").val(index).html(item));
  });

  // Use Javascript
  let redirecturl = "";
  // let href_split = window.location.href.split('/');
  let href = window.location.href;
  if (href.search("https://www.atlaschiroindia.com/backpain/") != -1) {
    redirecturl = "https://www.atlaschiroindia.com/backpain/thankyou/";
  } else if (
    href.search("https://www.atlaschiroindia.com/chiropractor/") != -1
  ) {
    redirecturl = "https://www.atlaschiroindia.com/chiropractor/thankyou/";
  } else {
    redirecturl = href + "/thankyou/";
  }
  // let redirecturl =  window.location.href.replace(href_split[href_split.length - 1],"")+"thankyou";
  // console.log(redirecturl);

  let today = new Date();
  today.setDate(today.getDate() + 1);
  let dd = today.getDate();
  let mm = today.getMonth() + 1; //January is 0 so need to add 1 to make it 1!
  let yyyy = today.getFullYear();
  if (dd < 10) {
    dd = "0" + dd;
  }
  if (mm < 10) {
    mm = "0" + mm;
  }

  today = yyyy + "-" + mm + "-" + dd; // Tomorrow
  $("#date").attr("min", today);

  const inputDate = document.getElementById("date");

  inputDate.addEventListener("click", function (evt) {
    this.showPicker();
  });

  inputDate.addEventListener("input", function (e) {
    var day = new Date(this.value).getUTCDay();
    if ([0].includes(day)) {
      e.preventDefault();
      this.value = "";
      alert("Sunday Holiday!");
      // e.target.setCustomValidity("Sunday Holiday!");
    }
  });

  $("#bookform").validate({
    // initialize the plugin on your form.
    // rules, options, and/or callback functions
    rules: {
      name: {
        required: true,
        minlength: 2,
      },
      phone: {
        required: true,
        minlength: 10,
        digits: true,
        maxlength: 10,
      },
      location: {
        required: true,
      },
      date: {
        required: true,
      },
    },
    submitHandler: function (form) {
      //   event.preventDefault();
      let name = $("#name").val();
      let phone = $("#phone").val();
      let location = $("#location").val();
      let date = $("#date").val();

      let data = {
        name: name,
        phone: phone,
        location: location,
        date: date,
      };
      $.post(
        "https://www.atlaschiroindia.com/sheetfeed/index.php",
        data,
        function (data) {
          //   console.log(data);
          $(".result").find(".alert").html("Thank you");
          $(".result")
            .show()
            .fadeOut(2500, function () {
              $("#bookform").trigger("reset");
              $("#exampleModal").modal("hide");
            });
          window.location.href = redirecturl;
        }
      );
      return false;
    },
  });

  //   $("#booknow").click(function (event) {});
});
