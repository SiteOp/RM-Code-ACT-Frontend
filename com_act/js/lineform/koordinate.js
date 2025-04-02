document.addEventListener("DOMContentLoaded", function () {
   let cxInput = document.getElementById('jform_cx');
   let cyInput = document.getElementById('jform_cy');

   cxInput.addEventListener('change', function () {
       let cx = cxInput.value;
       let textElement = document.querySelector('text.fas'); // Wähle das <text>-Element
       if (textElement) {
           textElement.setAttribute('x', cx);
       }
   });

   cyInput.addEventListener('change', function () {
       let cy = cyInput.value;
       let textElement = document.querySelector('text.fas');
       if (textElement) {
           textElement.setAttribute('y', cy);
       }
   });
});
