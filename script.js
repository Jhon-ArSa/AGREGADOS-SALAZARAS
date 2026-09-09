
// Carrusel de imágenes
const track = document.querySelector(".carrusel-track");
const slides = document.querySelectorAll(".slide");
const prevBtn = document.querySelector(".anterior");
const nextBtn = document.querySelector(".siguiente");
const paginacion = document.querySelector(".carrusel-paginacion");

let index = 0;

// Crear puntos
slides.forEach((_, i) => {
    const punto = document.createElement("span");
    if (i === 0) punto.classList.add("activo");
    punto.addEventListener("click", () => moverASlide(i));
    paginacion.appendChild(punto);
});

const puntos = document.querySelectorAll(".carrusel-paginacion span");

function actualizarCarrusel() {
    track.style.transform = `translateX(-${index * 100}%)`;
    puntos.forEach(p => p.classList.remove("activo"));
    puntos[index].classList.add("activo");
}

function moverASlide(i) {
    index = i;
    actualizarCarrusel();
}

prevBtn.onclick = () => {
    index = index === 0 ? slides.length - 1 : index - 1;
    actualizarCarrusel();
};

nextBtn.onclick = () => {
    index = index === slides.length - 1 ? 0 : index + 1;
    actualizarCarrusel();
};

// Auto slide
setInterval(() => {
    nextBtn.click();
}, 5000);

//carrusel fin 


//NOSTROS 

const counters = document.querySelectorAll('.estadistica-numero');

const runCounter = () => {
    counters.forEach(counter => {
        const target = +counter.getAttribute('data-target');
        let count = 0;
        const increment = target / 120;

        const update = () => {
            if (count < target) {
                count += increment;
                counter.innerText = Math.ceil(count);
                requestAnimationFrame(update);
            } else {
                counter.innerText = target;
            }
        };
        update();
    });
};

window.addEventListener('scroll', () => {
    const section = document.querySelector('.nosotros');
    if (section.getBoundingClientRect().top < window.innerHeight) {
        runCounter();
    }
}, { once: true });




//nostros fin 




// Selección de elementos (con defensas si no existen)

const menuIcon = document.querySelector("#menu-icon");
const navbar = document.querySelector(".navbar");

if (menuIcon && navbar) {
    // Evento para abrir/cerrar el menú
    menuIcon.addEventListener("click", () => {
        navbar.classList.toggle("open");
    });

    // Cerrar el menú al hacer clic en un enlace (opcional)
    document.querySelectorAll(".navbar a").forEach(link => {
        link.addEventListener("click", () => {
            navbar.classList.remove("open");
        });
    });
}








window.addEventListener("scroll", function () {
    let header = document.querySelector("header");
    if (window.scrollY > 50) { // Cuando el usuario baja más de 50px
        header.classList.add("scrolled");
    } else {
        header.classList.remove("scrolled");
    }
});

// Inserta el año actual en el footer si existe el span#anio
(function setYear() {
    try {
        const anioEl = document.getElementById('anio');
        if (anioEl) {
            const year = new Date().getFullYear();
            anioEl.textContent = year;
        }
    } catch (e) {
        // No interrumpir otras funcionalidades si ocurre algún error
        console.error('Error al establecer el año dinámico:', e);
    }
})();

// Manejo simple del enlace de saltar al contenido para accesibilidad
(function enableSkipLink() {
    const skip = document.querySelector('.skip-link');
    if (!skip) return;
    skip.addEventListener('click', (e) => {
        const targetId = skip.getAttribute('href')?.replace('#', '');
        if (!targetId) return;
        const target = document.getElementById(targetId);
        if (target) {
            target.setAttribute('tabindex', '-1');
            target.focus();
            // opcional: quitar tabindex después del blur
            target.addEventListener('blur', () => target.removeAttribute('tabindex'), { once: true });
        }
    });
})();

// Manejo del formulario de contacto
(function contactFormHandler() {
    const form = document.getElementById('contact-form');
    const statusEl = document.getElementById('contact-status');
    const DEST_EMAIL = 'jhonyaroni650@gmail.com'; // correo destino solicitado
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(form);
        const data = {
            nombre: formData.get('nombre') || '',
            email: formData.get('email') || '',
            telefono: formData.get('telefono') || '',
            mensaje: formData.get('mensaje') || ''
        };

        // Mostrar estado de envío
        if (statusEl) {
            statusEl.textContent = 'Enviando mensaje...';
        }

        // Intentar enviar con EmailJS si está disponible y configurado
        try {
            if (window.emailjs && typeof window.emailjs.send === 'function') {
                // Configura estos valores en emailjs y reemplaza aquí
                const SERVICE_ID = 'YOUR_SERVICE_ID';
                const TEMPLATE_ID = 'YOUR_TEMPLATE_ID';
                // la plantilla puede mapear: nombre, email, telefono, mensaje
                const templateParams = {
                    from_name: data.nombre,
                    from_email: data.email,
                    from_phone: data.telefono,
                    message: data.mensaje,
                    to_email: DEST_EMAIL
                };

                window.emailjs.send(SERVICE_ID, TEMPLATE_ID, templateParams)
                    .then(function () {
                        if (statusEl) statusEl.textContent = 'Mensaje enviado correctamente.';
                        form.reset();
                    }, function (err) {
                        console.error('EmailJS error:', err);
                        if (statusEl) statusEl.textContent = 'No se pudo enviar por email automático. Intentando abrir tu cliente de correo...';
                        // fallback a mailto
                        setTimeout(() => fallbackMailto(data), 800);
                    });

                return; // terminar handler
            }
        } catch (err) {
            console.error('Error al usar EmailJS:', err);
        }

        // Si EmailJS no está configurado o falla, usar mailto: como fallback
        fallbackMailto(data);
    });

    function fallbackMailto(data) {
        const subject = encodeURIComponent('Contacto desde sitio web - ' + (data.nombre || 'Sin nombre'));
        const bodyLines = [];
        bodyLines.push('Nombre: ' + data.nombre);
        bodyLines.push('Correo: ' + data.email);
        bodyLines.push('Teléfono: ' + data.telefono);
        bodyLines.push('');
        bodyLines.push('Mensaje:');
        bodyLines.push(data.mensaje);
        const body = encodeURIComponent(bodyLines.join('\n'));
        const mailto = `mailto:${DEST_EMAIL}?subject=${subject}&body=${body}`;

        // Intenta abrir cliente de correo
        window.location.href = mailto;
        if (statusEl) statusEl.textContent = 'Abriendo cliente de correo...';
    }
})();

// ── Preloader ────────────────────────────────────────────
window.addEventListener('load', () => {
    const preloader = document.getElementById('preloader');
    if (preloader) {
        preloader.classList.add('oculto');
        setTimeout(() => preloader.remove(), 600);
    }
});

// ── Botón volver arriba ──────────────────────────────────
const btnTop = document.getElementById('btn-top');
if (btnTop) {
    window.addEventListener('scroll', () => {
        btnTop.classList.toggle('visible', window.scrollY > 400);
    }, { passive: true });
    btnTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
}

// ── Scroll reveal ────────────────────────────────────────
(function initReveal() {
    const upSelectors = [
        '.agregados-titulo', '.agregados-descripcion',
        '.agregado-card',
        '.cobertura-header',
        '.servicios-header', '.servicio-card',
        '.nosotros-header',
        '.testimonio-card', '.testimonios-header',
        '.para-ti-titulo', '.para-ti-subtitulo',
        '.contacto-titulo', '.contacto-descripcion', '.contacto-card'
    ].join(', ');

    document.querySelectorAll(upSelectors).forEach(el => el.setAttribute('data-reveal', 'up'));
    document.querySelectorAll('.nosotros-info').forEach(el => el.setAttribute('data-reveal', 'left'));
    document.querySelectorAll('.estadisticas').forEach(el => el.setAttribute('data-reveal', 'right'));
    document.querySelectorAll('.para-ti-bloque').forEach((el, i) => {
        el.setAttribute('data-reveal', i % 2 === 0 ? 'left' : 'right');
    });

    const revealEls = document.querySelectorAll('[data-reveal]');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    revealEls.forEach(el => {
        const siblings = el.parentElement
            ? [...el.parentElement.querySelectorAll('[data-reveal]')]
            : [];
        const i = siblings.indexOf(el);
        if (i > 0) el.style.transitionDelay = `${i * 0.1}s`;
        observer.observe(el);
    });
})();

// ── Enlace activo en navbar al hacer scroll ───────────────
(function initActiveNav() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.navbar a[href^="#"]');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                navLinks.forEach(link => {
                    link.classList.toggle('active', link.getAttribute('href') === '#' + entry.target.id);
                });
            }
        });
    }, { threshold: 0.45 });
    sections.forEach(sec => observer.observe(sec));
})();

