var current = '';
var inicioMenu="";
var inicioComo="";
var inicioServicios="";
var inicioAreas="";
var inicioEmpleo="";
var inicioContacto="";
switch (current) {
    case 'inicio':
        inicioMenu = 'current';
        break;
    case 'como':
        inicioComo = 'current';
        break;
    case 'servicios':
        inicioServicios = 'current';
        break;
    case 'areas':
        inicioAreas = 'current';
        break;
    case 'empleo':
        inicioEmpleo = 'current';
        break;
    case 'contacto':
        inicioContacto = 'current';
        break;
}

var html ="<ul class='primary-nav'>";
html += "<li><a class='"+inicioMenu+"' href='index.html'>Inicio</a></li>";
html += "<li class='dropdown'>";
html += "<span class='dropdown-toggle span-menu "+inicioComo+"' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>¿Cómo trabajamos?<span class='caret'></span></span>";
html += "<ul class='dropdown-menu'>";
html += "<li><a href='filosofia.html'>Filosofía</a></li>";
html += "<li><a href='metodos-de-trabajo.html'>Métodos de trabajo</a></li>";
html += "<li><a href='quienes-somos.html'>Quienes somos</a></li>";
html += "</ul>";
html += "</li>";
html += "<li class='dropdown'>";
html += "<span class='dropdown-toggle span-menu "+inicioServicios+"' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>Servicios <span class='caret'></span></span>";
html += "<ul class='dropdown-menu'>";
html += "<li><a href='servicios-para-ninos-y-adolescentes.html'>Para niños y adolescentes</a></li>";
html += "<li><a href='servicios-para-padres.html'>Para padres</a></li>";
html += "<li><a href='servicios-para-centros-escolares.html'>Para Centros escolares</a></li>";
html += "<li><a href='servicios-online.html'>Servicios Online</a></li>";
html += "</ul>";
html += "</li>";
html += "<li><a class='"+inicioAreas+"' href='areas-de-intervencion.html'>Áreas de intervención</a></li>";
html += "<li><a class='"+inicioEmpleo+"' href='empleo.html'>Empleo</a></li>";
html += "<li><a class='"+inicioContacto+"' href='contacto.html'>Contacto</a></li>";
html += "</ul>";
html += "<ul class='member-actions'>";
html += "<li><a href='contacto.html' class='btn btn-accent btn-small'>Pedir cita</a></li>";
html += "</ul>";
document.write(html);

