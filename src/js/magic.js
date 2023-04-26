let methodType = 'PUT', functionType = '', html = '', data = {};
if (typeof (functionType) === 'undefined') {
    functionType = 'addRecord';
}

function ajaxStartStop() {
    $(document).ajaxStart(function () {
        $('#preloader').show();
    });
    $(document).ajaxStop(function () {
        setTimeout(function () {
            $('#preloader').hide();
        }, 1000);
    });
}

$(document).ready(function () {
    $(document).on('click', '#add-record, #edit-record', function (e) {
        e.preventDefault();
        if (this.id === 'add-record') {
            methodType = 'POST';
        }
        updateFormData();
        renderMagic();
    });

    $('#submit-form').submit(function (e) {
        e.preventDefault();
        let error = false;
        if (typeof (formValid) !== 'undefined' && !empty(formValid) && !$('#submit-form').valid()) {
            return false;
        } else {
            if (!empty(error)) {
                return false;
            } else {
                const formData = new FormData(this);
                $.ajax({
                    url: storeRoute,
                    method: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response.success === true) {
                            swal.fire('Success', response.message, 'success');
                            $('#submit-form')[0].reset();
                            if (typeof (indexRoute) !== 'undefined' && !empty(indexRoute)) {
                                window.location.href = indexRoute;
                            }
                        }
                    },
                    error: function (errors) {
                        let message = errors.responseJSON.message;
                        if (typeof (errors.responseJSON) !== 'undefined' && !empty(errors.responseJSON)) {
                            $.each(errors.responseJSON.errors, function (i, v) {
                                message += v + "\n" + '<br/>';
                            });
                        } else {
                            message = errors.statusText;
                        }
                        swal.fire("Error!", message, "error");
                    }
                })
            }
        }
    });

    $(document).on("click", '.paq-pager ul.pagination a', function (e) {
        e.preventDefault();
        page = $(this).attr('href').split('page=')[1];
        functionType = defaultType;
        updateFormData();
        renderMagic();
    });

    $('body').on('click', '.show_content', function () {
        id = $(this).attr('id').split('_')[1];
        functionType = 'showPopup';
        renderMagic();
    });

    $('body').on('click', '.delete_content', function (e) {
        e.preventDefault();
        const deleteId = this.id.split('_')[1];
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                functionType = 'delete';
                formData = {
                    '_token': token,
                    'id': deleteId
                };
                renderMagic();
            }
        });
    });

    $('body').on('click', '.sorting', function (e) {
        e.preventDefault();
        $('.sorting').not(this).removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
        sortColumn = $(this).attr("id");
        if ($(this).hasClass('fa-sort-' + asc)) {
            $(this).removeClass('fa-sort-' + asc).addClass('fa-sort-' + desc);
            sortType = 'desc';
        } else if ($(this).hasClass('fa-sort-' + desc)) {
            $(this).removeClass('fa-sort-' + desc).addClass('fa-sort-' + asc);
            sortType = 'asc';
        } else {
            $(this).addClass('fa-sort-' + asc);
            sortType = 'asc';
        }
        functionType = defaultType;
        updateFormData();
        renderMagic();
    });

    $('#search').keydown(function (e) {
        if (e.keyCode == 13) {
            e.preventDefault();
            search = $(this).val();
            page = 1;
            updateFormData();
            functionType = defaultType;
            renderMagic();
        }
    });
    /**
     * This is used to get drop down data dynamically
     */
    $('body .drop_down_filters').change(function () {
        dropDownFilters = {};
        const inputs = $(".drop_down_filters");
        for (var i = 0; i < inputs.length; i++) {
            dropDownFilters[$(inputs[i]).attr('id')] = $(inputs[i]).val();
        }
        updateFormData();
        functionType = defaultType;
        renderMagic();
    });

    $('body .number-only-class').keypress(function (event) {
        if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });
});

/**
 * This is used to control admin all functions
 */
function renderMagic() {
    /**
     * This is user to render grid data on base of grid fields
     */
    function renderGrid() {
        html = '';
        const result = data.result;
        const gridFields = data.gridFields;
        $('#total-record').html('[' + data.total + ']');
        $(".paq-pager").html(data.pager);
        if (!empty(result)) {
            $.each(result, function (i, v) {
                let keyValue = v;
                html += '<tr id="row_' + keyValue.id + '">';
                $.each(gridFields, function (index, value) {
                    if (value.name == 'image') {
                        html += '<td id="column_' + value.name + '_' + keyValue.id + '"><img src="' + keyValue.image + '"  style="height: 50px !important; width: 50px !important;"></td>';
                    } else {
                        const columnValue = v[value.name];
                        html += '<td id="column_' + value.name + '_' + keyValue.id + '"><span>' + isNull(columnValue) + '</span></td>';
                    }

                });
                let fn = window[defaultType];
                if (typeof fn === 'function') { // used to trigger relative action
                    fn(keyValue);
                }
                html += '</tr>';
            });
        }
        $('#page-data').html(html);
    };

    /**
     * This is used to render plan action
     */
    renderAction = function renderAction(keyValue) {
        const id = keyValue.id;
        html += '<td>';
        html += '<a href="' + editRoute + '/' + id + '/edit' + '" id="edit_' + id + '"  class="btn btn-primary btn-sm">Edit</a>' +
            '<a href="#"  id="delete_' + id + '" class="m-1 btn btn-danger btn-sm delete_content">Delete</a>\n' +
            '</td>';
    };

    /**
     * This is used to render grid routes
     */
    function callGridRender() {
        ajaxStartStop();
        $.ajax({
            url: renderRoute,
            type: 'POST',
            data: formData,
            success: function (response) {
                data = response;
                renderGrid();
            },
            error: function (errors) {
                let message = errors.responseJSON.message;
                if (typeof (errors.responseJSON) !== 'undefined' && !empty(errors.responseJSON)) {
                    $.each(errors.responseJSON.errors, function (i, v) {
                        message += v + "\n" + '<br/>';
                    });
                } else {
                    message = errors.statusText;
                }
                swal.fire("Error!", message, "error");
            }
        });
    };

    function sorter() {
        // will be add later
    };

    /**
     * This is common function used to add record
     */
    function addRecord() {
        $.ajax({
            url: addRecordRoute,
            type: methodType,
            data: formData,
            success: function (data) {
                if (data.success === true) {
                    swal.fire('Success', data.message, 'success');
                    window.location.href = indexRoute;
                }
            },
            error: function (errors) {
                let message = errors.responseJSON.message;
                if (typeof (errors.responseJSON) !== 'undefined' && !empty(errors.responseJSON)) {
                    $.each(errors.responseJSON.errors, function (i, v) {
                        message += v + "\n" + '<br/>';
                    });
                } else {
                    message = errors.statusText;
                }
                swal.fire("Error!", message, "error");
            }
        });
    }

    /**
     * This is used to render popup
     */
    function showPopup() {
        $.ajax({
            url: showRoute + '/' + id,
            type: 'GET',
            data: formData,
            success: function (response) {
                let html = '';
                let headers = response.headers;
                let popupData = response.popupData;
                $.each(headers, function (i, v) {
                    html += '<tr>';
                    html += '<td>' + v.name + '</td>';
                    html += '<td>' + isNull(popupData[v.key]) + '</td>';
                    html += '</tr>';
                });
                $('body #show-data').html(html);
            }
        });
    }

    /**
     * This is general function used to delete content
     */
    function destroy() {
        ajaxStartStop();
        $.ajax({
            url: deleteRoute + '/' + formData['id'],
            type: 'Delete',
            data: formData,
            success: function (data) {
                if (data.success == true) {
                    $('#preloader').hide();
                    swal.fire("Done!", data.message, "success");
                    updateFormData();
                    functionType = defaultType;
                    renderMagic();
                }
            },
            error: function (errors) {
                let message = errors.responseJSON.message;
                if (typeof (errors.responseJSON) !== 'undefined' && !empty(errors.responseJSON)) {
                    $.each(errors.responseJSON.errors, function (i, v) {
                        message += v + "\n" + '<br/>';
                    });
                } else {
                    message = errors.statusText;
                }
                swal.fire("Error!", message, "error");
            }
        });
    };
    // rendering grid
    if (functionType.indexOf('render') !== -1) {
        callGridRender();
    } else if (functionType.indexOf('delete') !== -1) {
        destroy();
    } else if (functionType.indexOf('addRecord') !== -1) {
        addRecord();
    } else if (functionType.indexOf('showPopup') !== -1) {
        showPopup();
    }
    let functionList = {};
    functionList["sorter"] = sorter;
    if (functionType in functionList) {
        functionList[functionType]();
    }
}

/**
 * This is used to check whether a value is empty
 *
 * @param val
 * @returns {boolean}
 */
function empty(val) {
    return !(!!val ? typeof val === 'object' ? Array.isArray(val) ? !!val.length : !!Object.keys(val).length : true : false);
}


/**
 * This is used to check value null or not
 *
 * @returns {*}
 */
function isNull(value) {
    if ((value === null && typeof value === "object") || typeof (value) === 'undefined') {
        return '';
    }
    return value;
}
