<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Flowchart Designer</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- jQuery -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    
    <!-- Flowchart JS -->
    <script src="{{ asset('js/flowchart.js') }}"></script>

    <script id="code">
        function init() {
            if (window.goSamples) goSamples();  // init for these samples -- you don't need to call this
              var $ = go.GraphObject.make;  // for conciseness in defining templates
              myDiagram =
                $(go.Diagram, "myDiagramDiv",  // must name or refer to the DIV HTML element
                  {
                    initialContentAlignment: go.Spot.Center,
                    allowDrop: true,  // must be true to accept drops from the Palette
                    "LinkDrawn": showLinkLabel,  // this DiagramEvent listener is defined below
                    "LinkRelinked": showLinkLabel,
                    "animationManager.duration": 800, // slightly longer than default (600ms) animation
                    "undoManager.isEnabled": true  // enable undo & redo
                  });
              // when the document is modified, add a "*" to the title and enable the "Save" button
              myDiagram.addDiagramListener("Modified", function(e) {
                var button = document.getElementById("SaveButton");
                if (button) button.disabled = !myDiagram.isModified;
                var idx = document.title.indexOf("*");
                if (myDiagram.isModified) {
                  if (idx < 0) document.title += "*";
                } else {
                  if (idx >= 0) document.title = document.title.substr(0, idx);
                }
              });
              // helper definitions for node templates
              function nodeStyle() {
                return [
                  // The Node.location comes from the "loc" property of the node data,
                  // converted by the Point.parse static method.
                  // If the Node.location is changed, it updates the "loc" property of the node data,
                  // converting back using the Point.stringify static method.
                  new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
                  {
                    // the Node.location is at the center of each node
                    locationSpot: go.Spot.Center,
                    //isShadowed: true,
                    //shadowColor: "#888",
                    // handle mouse enter/leave events to show/hide the ports
                    mouseEnter: function (e, obj) { showPorts(obj.part, true); },
                    mouseLeave: function (e, obj) { showPorts(obj.part, false); }
                  }
                ];
              }
              // Define a function for creating a "port" that is normally transparent.
              // The "name" is used as the GraphObject.portId, the "spot" is used to control how links connect
              // and where the port is positioned on the node, and the boolean "output" and "input" arguments
              // control whether the user can draw links from or to the port.
              function makePort(name, spot, output, input) {
                // the port is basically just a small circle that has a white stroke when it is made visible
                return $(go.Shape, "Circle",
                        {
                            fill: "transparent",
                            stroke: null,  // this is changed to "white" in the showPorts function
                            desiredSize: new go.Size(8, 8),
                            alignment: spot, alignmentFocus: spot,  // align the port on the main Shape
                            portId: name,  // declare this object to be a "port"
                            fromSpot: spot, toSpot: spot,  // declare where links may connect at this port
                            fromLinkable: output, toLinkable: input,  // declare whether the user may draw links to/from here
                            cursor: "pointer"  // show a different cursor to indicate potential link point
                        });
              }
              // define the Node templates for regular nodes
              var lightText = 'whitesmoke';
              myDiagram.nodeTemplateMap.add("",  // the default category
                $(go.Node, "Spot", nodeStyle(),
                  // the main object is a Panel that surrounds a TextBlock with a rectangular Shape
                  $(go.Panel, "Auto",
                    $(go.Shape, "Rectangle",
                      { fill: "#00A9C9", stroke: null },
                      new go.Binding("figure", "figure")),
                    $(go.TextBlock,
                      {
                        font: "bold 11pt Helvetica, Arial, sans-serif",
                        stroke: lightText,
                        margin: 8,
                        maxSize: new go.Size(160, NaN),
                        wrap: go.TextBlock.WrapFit,
                        editable: true
                      },
                      new go.Binding("text").makeTwoWay())
                  ),
                  // four named ports, one on each side:
                  makePort("T", go.Spot.Top, false, true),
                  makePort("L", go.Spot.Left, true, true),
                  makePort("R", go.Spot.Right, true, true),
                  makePort("B", go.Spot.Bottom, true, false)
              ));
              myDiagram.nodeTemplateMap.add("Terminator",
                $(go.Node, "Spot", nodeStyle(),
                  $(go.Panel, "Auto",
                    $(go.Shape, "RoundedRectangle",  // Bentuk rounded rectangle untuk terminator
                      { 
                        fill: "#79C900", 
                        stroke: null,
                        parameter1: 20  // Mengatur tingkat kelengkungan sudut
                      },
                      new go.Binding("fill", "color")),  // Binding untuk mengubah warna berdasarkan data
                    $(go.TextBlock,
                      {
                        font: "bold 11pt Helvetica, Arial, sans-serif",
                        stroke: lightText,
                        margin: 8,
                        maxSize: new go.Size(160, NaN),
                        wrap: go.TextBlock.WrapFit,
                        editable: true
                      },
                      new go.Binding("text").makeTwoWay())
                  ),
                  // Port untuk koneksi
                  makePort("T", go.Spot.Top, false, true),
                  makePort("L", go.Spot.Left, true, true),
                  makePort("R", go.Spot.Right, true, true),
                  makePort("B", go.Spot.Bottom, true, false)
              ));
              myDiagram.nodeTemplateMap.add("Start",
                $(go.Node, "Spot", nodeStyle(),
                  $(go.Panel, "Auto",
                    $(go.Shape, "Circle",
                      { minSize: new go.Size(40, 40), fill: "#79C900", stroke: null }),
                    $(go.TextBlock, "Start",
                      { font: "bold 11pt Helvetica, Arial, sans-serif", stroke: lightText },
                      new go.Binding("text"))
                  ),
                  // three named ports, one on each side except the top, all output only:
                  makePort("L", go.Spot.Left, true, false),
                  makePort("R", go.Spot.Right, true, false),
                  makePort("B", go.Spot.Bottom, true, false)
              ));
              myDiagram.nodeTemplateMap.add("End",
                $(go.Node, "Spot", nodeStyle(),
                  $(go.Panel, "Auto",
                    $(go.Shape, "Circle",
                      { minSize: new go.Size(40, 40), fill: "#DC3C00", stroke: null }),
                    $(go.TextBlock, "End",
                      { font: "bold 11pt Helvetica, Arial, sans-serif", stroke: lightText },
                      new go.Binding("text"))
                  ),
                  // three named ports, one on each side except the bottom, all input only:
                  makePort("T", go.Spot.Top, false, true),
                  makePort("L", go.Spot.Left, false, true),
                  makePort("R", go.Spot.Right, false, true)
              ));
              myDiagram.nodeTemplateMap.add("OnPageReference",
                $(go.Node, "Spot", nodeStyle(),
                  $(go.Panel, "Auto",
                    $(go.Shape, "Circle",
                      { 
                        fill: "#4B0082", 
                        stroke: "#4B0082",
                        strokeWidth: 2,
                        minSize: new go.Size(50, 50),
                        maxSize: new go.Size(80, 80)
                      }),
                    $(go.TextBlock,
                      {
                        font: "bold 14pt Helvetica, Arial, sans-serif",
                        stroke: "#4B0082",
                        margin: 5,
                        maxSize: new go.Size(60, NaN),
                        wrap: go.TextBlock.WrapFit,
                        editable: true,
                        textAlign: "center"
                      },
                      new go.Binding("text").makeTwoWay())
                  ),
                  // Port untuk koneksi
                  makePort("T", go.Spot.Top, false, true),
                  makePort("L", go.Spot.Left, true, true),
                  makePort("R", go.Spot.Right, true, true),
                  makePort("B", go.Spot.Bottom, true, false)
              ));
              myDiagram.nodeTemplateMap.add("OffPageReference",
                $(go.Node, "Spot", nodeStyle(),
                  $(go.Panel, "Auto",
                    $(go.Shape, {
                      geometry: go.Geometry.parse("M0 0 L160 0 L160 80 L80 110 L0 80 Z", true),  // Parameter true untuk filled
                      fill: "#FF8C00",     // Orange untuk fill
                      stroke: "#FF8C00",   // Orange untuk stroke
                      strokeWidth: 2
                    }),
                    $(go.TextBlock,
                      {
                        font: "bold 12pt Helvetica, Arial, sans-serif",
                        stroke: "white",  // Teks putih agar kontras dengan background orange
                        margin: 12,
                        maxSize: new go.Size(200, NaN),
                        wrap: go.TextBlock.WrapFit,
                        editable: true,
                        textAlign: "center"
                      },
                      new go.Binding("text").makeTwoWay())
                  ),
                  // Port untuk koneksi
                  makePort("T", go.Spot.Top, false, true),
                  makePort("L", go.Spot.Left, true, true),
                  makePort("R", go.Spot.Right, true, true),
                  makePort("B", go.Spot.Bottom, true, false)
              ));
              myDiagram.nodeTemplateMap.add("Comment",
                $(go.Node, "Auto", nodeStyle(),
                  $(go.Shape, "File",
                    { fill: "#556B2F", stroke: null }),
                  $(go.TextBlock,
                    {
                      margin: 5,
                      maxSize: new go.Size(200, NaN),
                      wrap: go.TextBlock.WrapFit,
                      textAlign: "center",
                      editable: true,
                      font: "bold 12pt Helvetica, Arial, sans-serif",
                      stroke: 'white'
                    },
                    new go.Binding("text").makeTwoWay())
                  // no ports, because no links are allowed to connect with a comment
              ));
              myDiagram.nodeTemplateMap.add("InputOutput",
                $(go.Node, "Spot", nodeStyle(),
                  $(go.Panel, "Auto",
                    $(go.Shape, "Parallelogram1",  // Bentuk parallelogram untuk Input/Output
                      { fill: "#FFA500", stroke: null }),  // Warna orange
                    $(go.TextBlock,
                      {
                        font: "bold 11pt Helvetica, Arial, sans-serif",
                        stroke: lightText,
                        margin: 8,
                        maxSize: new go.Size(160, NaN),
                        wrap: go.TextBlock.WrapFit,
                        editable: true
                      },
                      new go.Binding("text").makeTwoWay())
                  ),
                  // Port untuk koneksi
                  makePort("T", go.Spot.Top, false, true),
                  makePort("L", go.Spot.Left, true, true),
                  makePort("R", go.Spot.Right, true, true),
                  makePort("B", go.Spot.Bottom, true, false)
              ));
              myDiagram.nodeTemplateMap.add("ManualOperation",
                $(go.Node, "Spot", nodeStyle(),
                  $(go.Panel, "Auto",
                    $(go.Shape, "ManualOperation",  // Menggunakan bentuk bawaan GoJS
                      { 
                        fill: "#9370DB", 
                        stroke: "#9370DB",
                        strokeWidth: 2,
                        minSize: new go.Size(120, 60)
                      }),
                    $(go.TextBlock,
                      {
                        font: "bold 11pt Helvetica, Arial, sans-serif",
                        stroke: "white",
                        margin: 10,
                        maxSize: new go.Size(160, NaN),
                        wrap: go.TextBlock.WrapFit,
                        editable: true,
                        textAlign: "center"
                      },
                      new go.Binding("text").makeTwoWay())
                  ),
                  // Port untuk koneksi
                  makePort("T", go.Spot.Top, false, true),
                  makePort("L", go.Spot.Left, true, true),
                  makePort("R", go.Spot.Right, true, true),
                  makePort("B", go.Spot.Bottom, true, false)
              ));
              myDiagram.nodeTemplateMap.add("PredefinedProcess",
                $(go.Node, "Spot", nodeStyle(),
                  $(go.Panel, "Auto",
                    $(go.Shape, "Procedure",  
                      { 
                        fill: "#4682B4", 
                        stroke: "#87CEEB",  
                        strokeWidth: 2,
                        minSize: new go.Size(80, 50)
                      }),
                    $(go.TextBlock,
                      {
                        font: "bold 10pt Helvetica, Arial, sans-serif",
                        stroke: "white",  // Teks putih untuk kontras
                        margin: 8,
                        maxSize: new go.Size(120, NaN),
                        wrap: go.TextBlock.WrapFit,
                        editable: true,
                        textAlign: "center"
                      },
                      new go.Binding("text").makeTwoWay())
                  ),
                  // Port untuk koneksi
                  makePort("T", go.Spot.Top, false, true),
                  makePort("L", go.Spot.Left, true, true),
                  makePort("R", go.Spot.Right, true, true),
                  makePort("B", go.Spot.Bottom, true, false)
              ));
              myDiagram.nodeTemplateMap.add("Display",
                $(go.Node, "Spot", nodeStyle(),
                  $(go.Panel, "Auto",
                    $(go.Shape,
                      { 
                        geometry: go.Geometry.parse("M20 0 L160 0 Q180 0 180 20 L180 60 Q180 80 160 80 L20 80 L0 40 Z", true),
                        fill: "#FF69B4",     
                        stroke: "#FF69B4",   
                        strokeWidth: 2
                      }),
                    $(go.TextBlock,
                      {
                        font: "bold 11pt Helvetica, Arial, sans-serif",
                        stroke: "white",  // Teks putih untuk kontras
                        margin: 10,
                        maxSize: new go.Size(160, NaN),
                        wrap: go.TextBlock.WrapFit,
                        editable: true,
                        textAlign: "center"
                      },
                      new go.Binding("text").makeTwoWay())
                  ),
                  // Port untuk koneksi
                  makePort("T", go.Spot.Top, false, true),
                  makePort("L", go.Spot.Left, true, true),
                  makePort("R", go.Spot.Right, true, true),
                  makePort("B", go.Spot.Bottom, true, false)
              ));
              myDiagram.nodeTemplateMap.add("Preparation",
                $(go.Node, "Spot", nodeStyle(),
                  $(go.Panel, "Auto",
                    $(go.Shape, "Hexagon",  // Bentuk hexagon untuk preparation
                      { 
                        fill: "#8B4513",     // Saddle Brown - warna coklat
                        stroke: "#8B4513",   // Warna seragam
                        strokeWidth: 2,
                        minSize: new go.Size(100, 60)
                      }),
                    $(go.TextBlock,
                      {
                        font: "bold 11pt Helvetica, Arial, sans-serif",
                        stroke: "white",  // Teks putih untuk kontras
                        margin: 10,
                        maxSize: new go.Size(140, NaN),
                        wrap: go.TextBlock.WrapFit,
                        editable: true,
                        textAlign: "center"
                      },
                      new go.Binding("text").makeTwoWay())
                  ),
                  // Port untuk koneksi
                  makePort("T", go.Spot.Top, false, true),
                  makePort("L", go.Spot.Left, true, true),
                  makePort("R", go.Spot.Right, true, true),
                  makePort("B", go.Spot.Bottom, true, false)
              ));

              // replace the default Link template in the linkTemplateMap
              myDiagram.linkTemplate =
                $(go.Link,  // the whole link panel
                  {
                    routing: go.Link.AvoidsNodes,
                    curve: go.Link.JumpOver,
                    corner: 5, toShortLength: 4,
                    relinkableFrom: true,
                    relinkableTo: true,
                    reshapable: true,
                    resegmentable: true,
                    // mouse-overs subtly highlight links:
                    mouseEnter: function(e, link) { link.findObject("HIGHLIGHT").stroke = "rgba(30,144,255,0.2)"; },
                    mouseLeave: function(e, link) { link.findObject("HIGHLIGHT").stroke = "transparent"; }
                  },
                  new go.Binding("points").makeTwoWay(),
                  $(go.Shape,  // the highlight shape, normally transparent
                    { isPanelMain: true, strokeWidth: 8, stroke: "transparent", name: "HIGHLIGHT" }),
                  $(go.Shape,  // the link path shape
                    { isPanelMain: true, stroke: "gray", strokeWidth: 2 }),
                  $(go.Shape,  // the arrowhead
                    { toArrow: "standard", stroke: null, fill: "gray"}),
                  $(go.Panel, "Auto",  // the link label, normally not visible
                    { visible: false, name: "LABEL", segmentIndex: 2, segmentFraction: 0.5},
                    new go.Binding("visible", "visible").makeTwoWay(),
                    $(go.Shape, "RoundedRectangle",  // the label shape
                      { fill: "#F8F8F8", stroke: null }),
                    $(go.TextBlock, "Yes",  // the label
                      {
                        textAlign: "center",
                        font: "10pt helvetica, arial, sans-serif",
                        stroke: "#333333",
                        editable: true
                      },
                      new go.Binding("text").makeTwoWay())
                  )
                );
              // Make link labels visible if coming out of a "conditional" node.
              // This listener is called by the "LinkDrawn" and "LinkRelinked" DiagramEvents.
              function showLinkLabel(e) {
                var label = e.subject.findObject("LABEL");
                if (label !== null) label.visible = (e.subject.fromNode.data.figure === "Diamond");
              }
              // temporary links used by LinkingTool and RelinkingTool are also orthogonal:
              myDiagram.toolManager.linkingTool.temporaryLink.routing = go.Link.Orthogonal;
              myDiagram.toolManager.relinkingTool.temporaryLink.routing = go.Link.Orthogonal;
              load();  // load an initial diagram from some JSON text
              // initialize the Palette that is on the left side of the page
              myPalette =
                $(go.Palette, "myPaletteDiv",  // must name or refer to the DIV HTML element
                  {
                    "animationManager.duration": 800, // slightly longer than default (600ms) animation
                    nodeTemplateMap: myDiagram.nodeTemplateMap,  // share the templates used by myDiagram
                    model: new go.GraphLinksModel([  // specify the contents of the Palette
                      { category: "OnPageReference", text: "" },      // On-Page Reference
                      { category: "OffPageReference", text: "" },
                      { category: "Terminator", text: "Start/End"},
                      { text: "Process" },
                      { text: "Decision", figure: "Diamond" },
                      { category: "InputOutput", text: "Input/Output" },
                      { category: "ManualOperation", text: "Manual Process" },
                      { category: "Comment", text: "Comment" },
                      { category: "PredefinedProcess", text: "Predefined Process" },
                      { category: "Display", text: "Display" },
                      { category: "Preparation", text: "Preparation" }, 
                    ])
                  });
        }
        
        function showPorts(node, show) {
            var diagram = node.diagram;
            if (!diagram || diagram.isReadOnly || !diagram.allowLink) return;
            node.ports.each(function(port) {
                port.stroke = (show ? "white" : null);
              });
        }
        
        function save() {
            document.getElementById("mySavedModel").value = myDiagram.model.toJson();
            myDiagram.isModified = false;
        }
        
        function load() {
            myDiagram.model = go.Model.fromJson(document.getElementById("mySavedModel").value);
        }
        
        function makeSVG() {
            var svg = myDiagram.makeSvg({
              scale: 0.5
            });
            svg.style.border = "1px solid black";
            obj = document.getElementById("SVGArea");
            obj.appendChild(svg);
            if (obj.children.length > 0) {
              obj.replaceChild(svg, obj.children[0]);
            }
        }
        
        function downloadSVG() {
            var svg = myDiagram.makeSvg({
              scale: 1.0
            });
            var svgBlob = new Blob([svg.outerHTML], { type: "image/svg+xml" });
            var svgUrl = URL.createObjectURL(svgBlob);
            var link = document.createElement("a");
            link.href = svgUrl;
            link.download = "flowchart.svg";
            link.click();
        }

        function showSuccessMessage(message) {
          const notification = document.createElement('div');
          notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full';
          notification.innerHTML = `
              <div class="flex items-center">
                  <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                  </svg>
                  ${message}
              </div>
          `;
          
          document.body.appendChild(notification);
          
          setTimeout(() => {
              notification.classList.remove('translate-x-full');
          }, 100);
          
          setTimeout(() => {
              notification.classList.add('translate-x-full');
              setTimeout(() => {
                  document.body.removeChild(notification);
              }, 300);
          }, 3000);
        }

        function showErrorMessage(message) {
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full';
            notification.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    ${message}
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 5000);
        }

        function saveFlowchartToDatabase() {
            // Simpan model saat ini ke JSON
            save();
            
            // Ambil JSON data
            const jsonText = document.getElementById('mySavedModel').value;
            
            // Validasi JSON
            try {
                JSON.parse(jsonText);
            } catch (e) {
                alert('Data flowchart tidak valid!');
                return;
            }
            
            // Validasi apakah ada data
            if (!jsonText || jsonText.trim() === '') {
                alert('Tidak ada data flowchart untuk disimpan!');
                return;
            }
            
            // Update button state
            const saveButton = event.target;
            const originalText = saveButton.innerHTML;
            saveButton.innerHTML = 'Menyimpan...';
            saveButton.disabled = true;
            
            // Siapkan data untuk dikirim
            const requestData = {
                soal_id: {{ $data_soal->id }},
                flowchart_data: jsonText,
                encrypted_task: '{{ $encryptedTask }}'
            };
            
            // Kirim ke server
            fetch('{{ route("store-correct-answer", $encryptedQuestion) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessMessage('Flowchart berhasil disimpan! Mengalihkan...');
                    
                    // Langsung redirect tanpa delay dan tanpa display JSON
                    setTimeout(() => {
                        window.location.href = '{{ route("detail-tasks", ["id" => $encryptedTask]) }}';
                    }, 1000);
                } else {
                    showErrorMessage('Gagal menyimpan: ' + (data.message || 'Terjadi kesalahan'));
                    // Kembalikan button ke state normal jika error
                    saveButton.innerHTML = originalText;
                    saveButton.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorMessage('Terjadi kesalahan saat menyimpan flowchart');
                // Kembalikan button ke state normal jika error
                saveButton.innerHTML = originalText;
                saveButton.disabled = false;
            });
        }
        
        $(function(){
            $('#sample').trigger('onload');
        });
    </script>
</head>
<body class="bg-gray-50 min-h-screen" onload="init()">
    <div class="container mx-auto px-4 py-8">
        <div class="w-full">
          <div class="bg-white rounded-lg shadow-md p-4 mb-4">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Soal</h2>
            <div>
              <p>{{$data_soal->question}}</p>
            </div>
          </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-6">
          <div class="lg:w-1/4 w-full">
              <div class="bg-white rounded-lg shadow-md p-4 h-[600px]">
                  <h2 class="text-lg font-semibold text-gray-700 mb-4">Komponen</h2>
                  <div id="myPaletteDiv" class="border border-gray-200 rounded-md h-[520px]"></div>
              </div>
          </div>
          
          <div class="lg:w-3/4 w-full flex flex-col gap-4">
              <div class="bg-white rounded-lg shadow-md p-4 h-full">
                  <h2 class="text-lg font-semibold text-gray-700 mb-4">Area Gambar</h2>
                  <div id="myDiagramDiv" class="border border-gray-200 rounded-md h-[520px]">
                  </div>
              </div>  
          </div>
        </div>

        <div class="mt-4 bg-white rounded-lg shadow-md p-4 h-full">
            <div class="space-y-3">
                <button onclick="saveFlowchartToDatabase()" class="w-full py-2 px-4 bg-sky-600 hover:bg-sky-700 text-white font-medium rounded-md transition-colors">
                    Simpan
                </button>
                {{-- <button onclick="showJson()" class="w-full py-2 px-4 bg-sky-600 hover:bg-sky-700 text-white font-medium rounded-md transition-colors">
                    Lihat JSON
                </button> --}}
                <button onclick="window.location.href='{{ route('detail-tasks', ['id' => $encryptedTask]) }}'" class="w-full py-2 px-4 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-md transition-colors">
                    Kembali
                </button>
            </div>
        </div>

        <!-- JSON Display Area -->
        {{-- <div id="jsonDisplayArea" class="mt-8 hidden">
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">JSON Output</h2>
                    <div class="flex space-x-2">
                        <button onclick="copyJson()" class="py-1 px-3 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-md transition-colors">
                            Copy
                        </button>
                        <button onclick="document.getElementById('jsonDisplayArea').classList.add('hidden')" class="py-1 px-3 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm rounded-md transition-colors">
                            Close
                        </button>
                    </div>
                </div>
                <pre id="jsonDisplay" class="bg-gray-50 p-4 rounded-md overflow-x-auto text-sm"></pre>
            </div>
        </div> --}}
    </div>
      
    <div class="hidden mt-8">
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Model JSON:</h3>
        <textarea id="mySavedModel" class="w-full h-64 p-2 border border-gray-300 rounded-md">
          { 
            "nodeDataArray": [],
            "linkDataArray": []
          }
        </textarea>
    </div>
    <script type="text/javascript" src="{{ asset('js/mxgraph/javascript/mxClient.js') }}"></script>

    <script>
      function showJson() {
          // Simpan model saat ini ke JSON
          save();
          
          // Tampilkan modal atau area JSON
          const jsonArea = document.getElementById('jsonDisplayArea');
          jsonArea.classList.remove('hidden');
          
          // Scroll ke area JSON
          jsonArea.scrollIntoView({ behavior: 'smooth' });
      }
      function copyJson() {
          const jsonText = document.getElementById('mySavedModel').value;
          navigator.clipboard.writeText(jsonText).then(() => {
              alert('JSON copied to clipboard!');
          });
      }
      
      function showJson() {
          // Simpan model saat ini ke JSON
          save();
          
          // Format JSON untuk tampilan yang lebih baik
          const jsonText = document.getElementById('mySavedModel').value;
          try {
              const formattedJson = JSON.stringify(JSON.parse(jsonText), null, 2);
              document.getElementById('jsonDisplay').textContent = formattedJson;
          } catch (e) {
              document.getElementById('jsonDisplay').textContent = jsonText;
          }
          
          // Tampilkan area JSON
          document.getElementById('jsonDisplayArea').classList.remove('hidden');
      }
    </script>
</body>
</html>



