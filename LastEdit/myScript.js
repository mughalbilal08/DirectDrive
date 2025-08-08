 // Ensure all sections are closed when the page is loaded
 document.addEventListener("DOMContentLoaded", function() {
    const sections = document.querySelectorAll('.sidebar-sublist');
    sections.forEach(section => {
        section.style.display = 'none';
    });
});

function toggleSection(sectionId) {
    const sections = document.querySelectorAll('.sidebar-sublist');
    sections.forEach(section => {
        if (section.id === sectionId) {
            section.style.display = (section.style.display === 'none' || section.style.display === '') ? 'block' : 'none';
        } else {
            section.style.display = 'none';
        }
    });
}