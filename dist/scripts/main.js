$( document ).ready(function() {
  
    $('.simple-slider').slick({
        dots: false,
        arrow: true,
        infinite: true,
        speed: 600,
        slidesToShow: 1,
    }); 
  
    $('.home-banner').slick({
          dots: true,
          arrow: false,
          infinite: true,
          speed: 600,
          slidesToShow: 1,
    }); 

    $('.logos-list').slick({
        dots: false,
        arrow: false,
        infinite: false,
        speed: 600,
        slidesToShow: 1,
        slidesToShow: 4,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 3000,
        responsive: [
            {
              breakpoint: 700,
              settings: {
                slidesToShow: 2,
                slidesToScroll: 1,
                infinite: true,
              }
            }
        ]
  }); 
  
    $('.hamburger').click(function(){
        $("#header-menu").toggleClass('active');
        $("header").toggleClass('active-header');
    });
    
    $('.play-video').click(function(){
        $(".video-modal").toggleClass('active');
    });

   
    
});


$(window).scroll(function() {    
    var scroll = $(window).scrollTop();

    if (scroll >= 10) {
        $(".gallery-box").addClass("sticky-box"); 
    } else {
        $(".gallery-box").removeClass("sticky-box");
    }
});

 
$(document).ready(function(){

    $('.offerItemTitle').click(function(){

        if( $(this).parent('.offerItem').hasClass('active') ){
            $(this).parents('.offerslide').children('.offerItem').removeClass('active');
            $('#row-4').addClass('active'); 
        }else{ 
            $(this).parents('.offerslide').children('.offerItem').removeClass('active');
            //$(this).parents('.offerslide').children('.offerItem').children('.offerItemTitle').removeClass('hide');
            $(this).parent('.offerItem').addClass('active');  
            //$(this).addClass('hide'); 
        }

      

    });


    $('.offer-content .arrow').click(function(){
        var idrow = $(this).attr('att-step');
        $('#'+idrow+' .offerItemTitle').click();

    });

});


jQuery(document).ready(function() {
	
    // Move #cm7-progress to the top of .wpcf7-form
    jQuery('.wpcf7-form').prepend(jQuery('#cm7-progress'));

    var number_of_columns = jQuery('#cm7-progress').attr('columns');
    
    if(number_of_columns == 'steps-3'){
       jQuery('#per-progress').css('width','16.5%');
    }else{
       jQuery('#per-progress').css('width','12.5%');
    }

   jQuery('.wpcf7-list-item').css('display','inline-block');
   // Attach change event listener to all checkboxes within the specified container
   jQuery('.wpcf7-list-item input[type="checkbox"]').change(function() {
       // Check if the checkbox is checked
       if (jQuery(this).is(':checked')) {
           // Add the "active" class to the parent .wpcf7-list-item
           jQuery(this).closest('.wpcf7-list-item').addClass('active');
       } else {
           // Remove the "active" class from the parent .wpcf7-list-item
           jQuery(this).closest('.wpcf7-list-item').removeClass('active');
       }
   });


 
   // Function to handle class and attribute changes
   function handleChanges(mutationsList, observer) {
       mutationsList.forEach(function(mutation) {
           // Check if nodes are added
           if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
               // Get the target element that was mutated
               var targetElement = mutation.target;

               // Check if the target element has the desired class added
               if (jQuery(targetElement).hasClass('cf7mls_current_fs')) {
                   // Get the value of the data-cf7mls-order attribute (pos)
                   var pos = parseInt(jQuery(targetElement).attr('data-cf7mls-order'));

                   var number_of_columns = jQuery('#cm7-progress').attr('columns');
                   var service_title = jQuery('#cm7-progress').attr('service');


                   if (jQuery('#service-title').length) {
                       jQuery('#service-title').val(service_title);
                   }


                   // Perform the calculation (pos + 25) / 2
                   if(number_of_columns == 'steps-3'){
                       pos = pos + 1;
                       var result = (pos * 33)-16.5 ;

                   }else{
                       pos = pos + 1;
                       var result = (pos * 25)-12.5 ;

                   }


                   // Log the result to the console (or perform other actions)
                  console.log('Rst:'+pos, result);
                  jQuery('#per-progress').css('width',result+'%');


                   jQuery('.bar-item').removeClass('active');

                   
                   for (let i = 1; i <= pos; i++) {
                       console.log('pos', i);
                       jQuery('#item-'+i).addClass('active');
                   }


                   
               }
           }
       });
   }

   // Create a new MutationObserver
   var observer = new MutationObserver(handleChanges);

   // Define the options for the observer
   var observerOptions = {
       attributes: true,             // Watch for attribute changes
       attributeFilter: ['class'],   // Only watch for changes to the 'class' attribute
       subtree: true                 // Watch for changes in the entire subtree of the target node
   };

   // Start observing the document body for changes
   observer.observe(document.body, observerOptions);

});
 

document.addEventListener('wpcf7mailsent', function(event) {
    location = 'https://www.dsa-connect.co.uk/contact/?thank=1&step=1';
}, false);

