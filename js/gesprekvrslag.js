const inputDiv = document.querySelector(".input-div")
const imageInput = document.querySelector("input.image-input")
const output = document.querySelector("output")
let imagesArray = []


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

function addPerson()
{
    var html = "<input type='text' name='gw_person[]' class='gw-todo-input' placeholder='Voornaam Achternaam' />";
    $(".gw-todo-list-box.gw-personen").append(html);
}

function fileChanged()
{
    const files = imageInput.files
    for (let i = 0; i < files.length; i++) {
        imagesArray.push(files[i])
    }
    displayImages();
}

function fileDroped(e)
{
    e.preventDefault()
    const files = e.dataTransfer.files
    for (let i = 0; i < files.length; i++) {
        if (!files[i].type.match("image")) continue;

        if (imagesArray.every(image => image.name !== files[i].name))
        imagesArray.push(files[i])
    }
    displayImages()
}
function displayImages()
{
    let images = ""
    imagesArray.forEach((image, index) => {
      images += `<div class="image">
                  <img src="${URL.createObjectURL(image)}" alt="image">
                  <span onclick="deleteImage(${index})">&times;</span>
                </div>`
    })
    output.innerHTML = images
}

function deleteImage(index) {
    imagesArray.splice(index, 1)
    displayImages()
    $(".image-input").val("");
  }

function clearAllInputs()
{
    var formData = new FormData();
    if(imagesArray.length > 0)
    {
        for(var index = 0; index < imagesArray.length; index ++)
        {
            formData.append('files[]',imagesArray[index]);
        }
        
    }
    $.ajax({
        type: "POST",
        url: "../php/settings/upload_gw_image.php",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function(result) {

                if(result['message'] == 'success')
                {
                    $(".image_path").remove();
                    var images = result['paths'];
                    for(var index = 0; index < images.length; index ++)
                    {
                        var html = "<input hidden value='" + images[index] + "' class='image_path' name='image_path[]'/>";
                        $("#gp-form").append(html);
                    }
                    var frm = document.getElementById('gp-form');
                    frm.submit(); // Submit
                    $(".image_path").remove();
                    return false; // Prevent page refresh
                }
                
        },
        error: function(e1, e2, e3)
        {
            debugger;
        }
    });


}

function changedGpProject()
{
    $("#gw-voornaam").text($("#gp_project option:selected").attr('data-name'));
}