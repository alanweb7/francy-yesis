loading = true;

Application = {};
Application.translation = {
    'en' : {
        'loading' : 'Loading',
        'close'   : 'Close'
    },
    'pt' : {
        'loading' : 'Carregando',
        'close'   : 'Fechar'
    },
    'es' : {
        'loading' : 'Cargando',
        'close'   : 'Cerrar'
    }
};

Adianti.onClearDOM = function(){
	/* $(".select2-hidden-accessible").remove(); */
	/* $(".colorpicker-hidden").remove(); */
	$(".pcr-app").remove();
	$(".select2-display-none").remove();
	$(".tooltip.fade").remove();
	$(".select2-drop-mask").remove();
	/* $(".autocomplete-suggestions").remove(); */
	$(".datetimepicker").remove();
	$(".note-popover").remove();
	$(".dtp").remove();
	$("#window-resizer-tooltip").remove();
};


function showLoading() 
{ 
    if(loading)
    {
        __adianti_block_ui(Application.translation[Adianti.language]['loading']);
    }
}

Adianti.onBeforeLoad = function(url) 
{ 
    loading = true; 
    setTimeout(function(){showLoading()}, 400);
    if (url.indexOf('&static=1') == -1) {
        $("html, body").animate({ scrollTop: 0 }, "fast");
    }
};

Adianti.onAfterLoad = function(url, data)
{ 
    loading = false; 
    __adianti_unblock_ui( true );
    
    // Fill page tab title with breadcrumb
    // window.document.title  = $('#div_breadcrumbs').text();
};

// set select2 language
$.fn.select2.defaults.set('language', $.fn.select2.amd.require("select2/i18n/pt"));

function tentry_mask(field, event, mask)
{
    var value, i, character, returnString,tamCampo,maskLength;
    value = field.value;
    
    if (typeof value == 'undefined')
    {
        return true;
    }
    
    if ($(field).attr('forceupper') == '1')
    {
        value = value.toUpperCase();
    }
    if ($(field).attr('forcelower') == '1')
    {
        value = value.toLowerCase();
    }
    
    if(document.all) // IE
    {
        keyCode = event.keyCode;
    }
    else if(document.layers) // Firefox
    {
        keyCode = event.which;
    }
    else
    {
        keyCode = event.which;
    }
    if (keyCode == 8 || event.keyCode == 9 || event.keyCode == 13) // backspace e caps
    {
        return true;
    }
    
    returnString = '';
    var n = 0;
    
    /**
     * Mask Type Verification 
     * Verifica se a mascará será aplicada caracter a caracter
     * ou se será aplicada a todo o campo.
     * ! = aplicada a todo o campo + case sensitive
     * # = aplicada a todo o campo - case sensitive
     */
    if(mask.charAt(1)=='!')
    {
       maskLength = field.value.length+1;
    }
    else
    {
       maskLength = mask.length;
    }
    
    for(i=0; i<maskLength-1; i++)
    {
        maskChar  = mask.charAt(i);
        valueChar = value.charAt(n);
        
        if (i <= value.length)
        {
            if (((maskChar == "-")  || (maskChar == "_") || (maskChar == ".") || (maskChar == "/") ||
            (maskChar == ' ') || (maskChar == "\\") || (maskChar == ":") || (maskChar == "|") ||
                 (maskChar == "(")  || (maskChar == ")") || (maskChar == "[") || (maskChar == "]") ||
                 (maskChar == "{")  || (maskChar == "}")) & (maskChar!==valueChar))
            {
                returnString += maskChar; 
            }
            else
            {
            
                returnString += valueChar;
                n ++;
            }
        }
    }
    
    field.value = returnString;
    tamCampo    = field.value.length;


    /**
     * Mask Character Verification 
     * ! - todo campo
     *
     * Verifica o segundo campo da mascara.
     * Se ='!' aplica a mascara a todo o campo
     * Senão aplica a mascara definida para cada caractere. 
     */
    if(mask.charAt(1)=='!' )
    {
        maskChar = mask.charAt(0);
    }
    else
    {
        maskChar = mask.charAt(tamCampo);
    }

    /**
     * Mask Verification 
     * A,a - campo alfanumerico
     * S,s - alfabetico  
     * 9 - numeros
     *  
     * Verifica a mascara definida para o campo
     */
    switch(maskChar)
    {
        
        case 'A':
        case 'a':
            return (((keyCode > 47) && (keyCode < 58))||((keyCode > 64) && (keyCode < 91))||((keyCode > 96) && (keyCode < 123)));
            break;
        case 'S':
        case 's':
            return (((keyCode > 64) && (keyCode < 91))||((keyCode > 96) && (keyCode < 123)));
            break;
        case '9':
            return ((keyCode > 47) && (keyCode < 58));
            break;
    }

    return true;
}
