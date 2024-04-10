var activePage = null, activePageObj = null;


$(document).ready(function(){

    $('[data-toggle="tooltip4b"]').tooltip({
        placement: "bottom"
    });

    var initVersiteBuilder = function(){
        console.log("Versite Builder initialize");
    }

    var loadingVB = function() {
        setTimeout(function() {
            if(loaded) {
                initVersiteBuilder();
            } else {
                loadingVB();
            }
        }, 500);
    };

    loadingVB();

    $("#topfixed").on("click", ".section-edit", function(){

        activePageObj = builder.getActivePageObject();
        activePage = activePageObj.getDOMSelf();

        if(!activePage) {
            return;
        }

        if($(this).is(".active")){
            //alert("Q");
        }

        $('.section-edit').removeClass('active');
        var pageName = replaceSpace(activePageObj.getPageName());
        var modalFormContainer = document.getElementById('modal-form-container');
        builder.clearControlElements(activePage);
        builder.clearControlElements(modalFormContainer);

        builder._changePageMode(activePage, this.id);
        builder._setControlsElement(this.id != 'edit-sections' ? pageName : null, this.id);
        builder._controlPanel.sections.self.classList.remove('show');
        builder._hideSections(builder);
        builder._hideControlPanel(builder);

        $(this).addClass('active');
    });

    $("#topfixed").on("click", ".nav-settings", function() {
        $('.section-edit').removeClass('active');
        $("#" + builder.getPageMode()).addClass('active');
        //builder._showControlPanel(builder);
    });

});
