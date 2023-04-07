function addTodoVoor()
{
    var html = "<input type='text' name='gw_todo_voornaam[]' class='gw-todo-input'/>";
    $(".gw-todo-list-box.gw-todo-voor").append(html);
    $(".gw-todo-list-box.gw-todo-voor input").each((index, item) =>{
        $(item).attr('placeholder', "#" + (index + 1));
    });
}

function addTodoZeeuw()
{
    var html = "<input type='text' name='gw_todo_zeeuw[]' class='gw-todo-input'/>";
    $(".gw-todo-list-box.gw-todo-zeeuw").append(html);
    $(".gw-todo-list-box.gw-todo-zeeuw input").each((index, item) =>{
        $(item).attr('placeholder', "#" + (index + 1));
    });
}

function clearAllInputs()
{
    var frm = document.getElementById('gp-form');
   frm.submit(); // Submit
//    frm.reset();  // Reset
   return false; // Prevent page refresh
}
