function imprimeEtat() {
    // Lancer l'impression
    window.print();
    window.onafterprint = function(){
        window.close();
    }
}
