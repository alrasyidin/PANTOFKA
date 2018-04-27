function hideSection(sectionId) {
    var section = document.getElementById(sectionId);
    section.innerHTML = '';
    section.visibility = 'hidden';
    return true;
}