let cx = document.getElementById('jform_cx');
cx.addEventListener ('change', function () {
   let cx = document.getElementById('jform_cx').value;
   var circle = document.getElementById('circle');
	circle.setAttribute('cx', cx);
});


let cy = document.getElementById('jform_cy');
cy.addEventListener ('change', function () {
   let cy = document.getElementById('jform_cy').value;
   var circle = document.getElementById('circle');
	circle.setAttribute('cy', cy);
});
