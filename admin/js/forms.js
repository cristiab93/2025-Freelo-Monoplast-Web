function DeleteTag(id)
{
    if(confirm("¿Estás seguro de eliminar este formulario? (Esta acción es irreversible)")){
        console.log("form eliminado");
        document.getElementById("form_delete_" + id).submit();
    }
    else{
        return false;
    }
}
