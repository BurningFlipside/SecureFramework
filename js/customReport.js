let SwaggerParser = require('@apidevtools/swagger-parser');
let knownApis = [
  {
    Name: "Tickets",
    OpenAPI: "tickets/api/v1/openapi.json",
  }
];

function createErrorAlert(response) {
  let errorAlert = document.createElement("div");
  errorAlert.setAttribute("class", "alert alert-danger");
  errorAlert.setAttribute("role", "alert");
  switch(response.status) {
    case 401:
      errorAlert.innerText = "You aren't logged in...";
      break;
    default:
      errorAlert.innerText = "Failed to get data";
      break;
  }
  return errorAlert;
}

function showSingleObject(path, obj, data, content) {
  let notice = document.createElement("div");
  notice.setAttribute("class", "alert alert-primary");
  notice.setAttribute("role", "alert");
  notice.innerText = "This API returns only one item.";
  content.appendChild(notice);

  let returnDataAreaDiv = document.createElement("div");
  returnDataAreaDiv.setAttribute("class", "mb-3");
  let returnDataAreaLabel = document.createElement("label");
  returnDataAreaLabel.setAttribute("class", "form-label");
  returnDataAreaLabel.innerText = "Returned Data";
  returnDataAreaDiv.appendChild(returnDataAreaLabel);
  let returnDataArea = document.createElement("textarea");
  returnDataArea.setAttribute("class", "form-control flex-grow-1");
  returnDataArea.setAttribute("id", "returnDataArea");
  returnDataArea.setAttribute("rows", "10");
  returnDataAreaDiv.appendChild(returnDataArea);
  content.appendChild(returnDataAreaDiv);
  fetch(data.servers[0].url + path).then((response) => {
    if(response.ok) {
      return response.json();
    }
    content.appendChild(createErrorAlert(response));
    return {};
  }).then((data) => {
    returnDataArea.value = JSON.stringify(data, null, 2);
  }).catch((error) => {
    alert("Failed to get data");
    console.error(error);
  });
}

function headerMenuDisplay(e, column, table, dataType) {
  let menu = [];
  let columnDefinition = column.getDefinition();
  let label = document.createElement("span");
  label.innerText = 'Hide Column';
  let cols = table.getColumns();
  menu.push({
    label:label,
    action:function(e){
        //prevent menu closing
        e.stopPropagation();
        column.toggle();
        setSelectField(cols);
    }
  });
  switch(dataType.type) {
    case 'integer':
    case 'number':
      label = document.createElement("span");
      label.innerText = 'Math Functions';
      menu.push({
        label: label,
        menu: [
          {
            label: 'Equals',
            action: function(e) {
              PlainBSModal.prompt({
                title: "Equals",
                inputType: 'number',
              }).then((result) => {
                table.addFilter(column.getField(), '=', result.value);
                addFilter(column.getField() +' eq '+result.value);
              }).catch();
            }
          }
        ]
      });
  }
  return menu;
}

function setURIField(paramName, paramValue) {
  let uriField = document.getElementById("uriInput");
  if(!uriField) {
    console.log("No URI field");
    return;
  }
  const url = new URL(uriField.value);
  url.searchParams.set(paramName, paramValue);
  let str = url.toString();
  uriField.value = str.replaceAll('%24', '$').replaceAll('%2C', ',');
}

function setSelectField(columns) {
  let visibleColumns = columns.filter((column) => column.isVisible());
  let selectContent = visibleColumns.map((column) => column.getField()).join(',');
  setURIField('$select', selectContent);
}

function addFilter(filterClause) {
  let uriField = document.getElementById("uriInput");
  if(!uriField) {
    console.log("No URI field");
    return;
  }
  const url = new URL(uriField.value);
  let filter = url.searchParams.get('$filter');
  if(filter) {
    filter += ' and ' + filterClause;
  } else {
    filter = filterClause;
  }
  setURIField('$filter', filter);
}

function showReportData(path, obj, data, content) {
  let uriDiv = document.createElement("div");
  uriDiv.setAttribute("class", "mb-3 input-group");
  let uriLabel = document.createElement("span");
  uriLabel.setAttribute("class", "input-group-text");
  uriLabel.innerText = "URI";
  uriDiv.appendChild(uriLabel);
  let uriInput = document.createElement("input");
  uriInput.setAttribute("class", "form-control");
  uriInput.setAttribute("id", "uriInput");
  uriInput.setAttribute("type", "text");
  uriInput.setAttribute("value", data.servers[0].url + path);
  uriInput.setAttribute("readonly", "readonly");
  uriDiv.appendChild(uriInput);
  let uriButton = document.createElement("button");
  uriButton.setAttribute("class", "btn btn-outline-secondary");
  uriButton.setAttribute("type", "button");
  uriButton.setAttribute("id", "uriButton");
  uriButton.innerText = "Copy";
  uriButton.addEventListener("click", function() {
    let copyText = uriInput.value;
    navigator.clipboard.writeText(copyText);
  });
  uriDiv.appendChild(uriButton);
  uriButton = document.createElement("button");
  uriButton.setAttribute("class", "btn btn-outline-secondary");
  uriButton.setAttribute("type", "button");
  uriButton.setAttribute("id", "refresh");
  uriButton.innerHTML = '<i class="fa-solid fa-rotate"></i>';
  uriButton.addEventListener("click", function() {
    let uriField = document.getElementById("uriInput");
    if(!uriField) {
      console.log("No URI field");
      return;
    }
    const url = new URL(uriField.value);
    let top = url.searchParams.get('$top');
    let topCount = 0;
    if(!top) {
      url.searchParams.set('$top', 10);
      topCount = 10;
    } else {
      topCount = parseInt(top);
    }
    let count = url.searchParams.get('$count');
    if(!count) {
      url.searchParams.set('$count', true);
    }
    fetch(url.toString()).then((response) => {
      if(response.ok) {
        return response.json();
      }
      content.appendChild(createErrorAlert(response));
      return {};
    }).then((data) => {
      let table = Tabulator.findTable('#returnDataTable')[0];
      table.replaceData(data.value);
      if(data['@odata.count'] > topCount) {
        if(document.getElementById("moreDataAlert")) {
          return;
        }
        let alertDiv = document.createElement("div");
        alertDiv.setAttribute("class", "alert alert-info");
        alertDiv.setAttribute("role", "alert");
        alertDiv.setAttribute("id", "moreDataAlert");
        alertDiv.innerText = "The final report will contain more data. It has been limited here to reduce load times.";
        content.appendChild(alertDiv);
      }
    });
  });
  uriDiv.appendChild(uriButton);
  content.appendChild(uriDiv);
  let returnDataTable = document.createElement("table");
  returnDataTable.setAttribute("class", "table");
  returnDataTable.setAttribute("id", "returnDataTable");
  content.appendChild(returnDataTable);
  let tabConfig = {
    ajaxURL: data.servers[0].url + path,
    columns: []
  };
  for(let [key, value] of Object.entries(obj.responses["200"].content["application/json"].schema.items.properties)) {
    let column = {
      title: key,
      field: key,
      headerMenu: function(e, column) {
        return headerMenuDisplay(e, column, this, value);
      },
    };
    if(value.description) {
      column.headerTooltip = value.description;
    }
    if(value.type === 'boolean') {
      column.formatter = 'tickCross';
    }
    tabConfig.columns.push(column);
  }
  let table = new Tabulator('#returnDataTable', tabConfig);
  table.on("dataLoadError", function(error){
    let errorAlert = createErrorAlert(error);
    content.appendChild(errorAlert);
  });
  table.on("dataSorted", function(sorters){
    if(sorters.length === 0) {
      return;
    }
    let orderBy = sorters.map((sorter) => sorter.field + ' ' + sorter.dir).join(',');
    setURIField('$orderby', orderBy);
});
}

function getEndpointData(path, obj, data) {
  const content = document.getElementById("content");
  if(!content) {
    return;
  }
  content.innerHTML = "";
  if(obj.description) {
    let description = document.createElement("div");
    description.setAttribute("class", "row");
    description.innerText = obj.description;
    content.appendChild(description);
  }
  let dataSchema = obj.responses["200"].content["application/json"].schema;
  switch(dataSchema.type) {
    case 'object':
      return showSingleObject(path, obj, data, content); 
    case 'array':
      /*This is the good case...*/
      return showReportData(path, obj, data, content);
    default:
      console.log("Unknown schema type: " + dataSchema.type);
  }
}

function createEndpointSelect(data) {
  const endpointSelect = document.getElementById("endpointSelect");
  if(!endpointSelect) {
    return;
  }
  let dropdown = document.createElement("select");
  dropdown.setAttribute("class", "form-select");
  dropdown.setAttribute("id", "endpointSelect");
  for(let [path, obj] of Object.entries(data.paths)) {
    if(obj.get === undefined) {
      continue;
    }
    let option = document.createElement("option");
    option.value = path;
    option.innerText = path + ' - ' + obj.get.summary;
    dropdown.appendChild(option);
  }
  dropdown.addEventListener("change", function() {
    getEndpointData(this.value, data.paths[this.value].get, data);
  });
  endpointSelect.appendChild(dropdown);
  getEndpointData(dropdown.value, data.paths[dropdown.value].get, data);
}

function createAPIMenu(data) {
  let parser = new SwaggerParser();
  parser.dereference(data).then((data) => {
    createEndpointSelect(data);
  }).catch((error) => {
    alert("Failed to parse API");
    console.error(error);
  });
}

function loadAPI() {
  let apiURL = this.dataset.api;
  fetch(apiURL).then((response) => {
    if(response.ok) {
      return response.json();
    }
    throw new Error("Failed to load API");
  }).then((data) => {
    createAPIMenu(data);
  }).catch((error) => {
    alert("Failed to load API");
    console.error(error);
  });
}

function initPage() {
  const apiSelect = document.getElementById("apiMenu");
  if(!apiSelect) {
    return;
  }
  for(let api of knownApis) {
    let entry = document.createElement("li");
    let link = document.createElement("a");
    link.setAttribute("class", "dropdown-item");
    link.innerText = api.Name;
    link.dataset.api = api.OpenAPI;
    link.addEventListener("click", loadAPI);
    entry.appendChild(link);
    apiSelect.appendChild(entry);
  }
}

window.onload = initPage;