@extends('layout-admins.app')

@section('csrf-token')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('onload')
    onload="init()"
@endsection

@section('content')
    <div class="container mx-auto px-4 py-8 min-h-screen">
        <div class="w-full mb-6">
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center mb-4">
                    <i class="bi bi-grid-3x3-gap-fill text-3xl text-blue-500 mr-3"></i>
                    <h2 class="text-2xl font-bold text-gray-800">Soal</h2>
                </div>
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-gray-700 leading-relaxed">{{$data_soal->question}}</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-6">
            <div class="lg:w-1/4 w-full">
                <div class="bg-white rounded-xl shadow-lg p-4 h-[600px] border border-gray-200">
                    <div class="flex items-center mb-4">
                        <i class="bi bi-tools text-xl text-indigo-500 mr-2"></i>
                        <h2 class="text-lg font-semibold text-gray-700">Komponen Flowchart</h2>
                    </div>
                    <div id="myPaletteDiv" class="border-2 border-dashed border-gray-300 rounded-lg h-[520px] bg-gray-50 hover:border-indigo-300 transition-colors duration-300"></div>
                </div>
            </div>
            
            <div class="lg:w-3/4 w-full flex flex-col gap-4">
                <div class="bg-white rounded-xl shadow-lg p-4 h-full border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <i class="bi bi-pencil-square text-xl text-green-500 mr-2"></i>
                            <h2 class="text-lg font-semibold text-gray-700">Area Gambar Flowchart</h2>
                        </div>
                    </div>
                    <div id="myDiagramDiv" class="border-2 border-dashed border-gray-300 rounded-lg h-[520px] bg-gradient-to-br from-gray-50 to-white hover:border-green-300 transition-colors duration-300 relative overflow-hidden">
                        <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle, #e5e7eb 1px, transparent 1px); background-size: 20px 20px;"></div>
                    </div>
                </div>  
            </div>
        </div>

        <div class="mt-6 bg-white rounded-xl shadow-lg p-6 border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <button onclick="window.location.href='{{ route('detail-tasks', ['id' => $encryptedTask]) }}'" class="group relative overflow-hidden bg-gradient-to-r from-gray-400 to-gray-500 hover:from-gray-500 hover:to-gray-600 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <div class="flex items-center justify-center">
                        <i class="bi bi-arrow-left text-lg mr-2"></i>
                        <span>Kembali</span>
                    </div>
                    <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                </button>

                <button onclick="event.preventDefault(); showConfirmation(saveFlowchartToDatabase)" class="group relative overflow-hidden bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <div class="flex items-center justify-center">
                        <i class="bi bi-floppy-fill mr-2"></i>
                        <span>Simpan</span>
                    </div>
                    <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                </button>
            </div>
        </div>

        <div class="hidden mt-8">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Model JSON:</h3>
            <textarea id="mySavedModel" class="w-full h-64 p-2 border border-gray-300 rounded-md bg-gray-50">
                { 
                    "nodeDataArray": [],
                    "linkDataArray": []
                }
            </textarea>
        </div>

        <div class="fixed bottom-6 right-6 z-50">
            <button class="bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white p-4 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110" onclick="showHelp()">
                <i class="bi bi-person-raised-hand text-xl"></i>
            </button>
        </div>
    </div>

    <style>
        @keyframes pulse-border {
            0%, 100% { border-color: #e5e7eb; }
            50% { border-color: #6366f1; }
        }
        
        .animate-pulse-border {
            animation: pulse-border 2s ease-in-out infinite;
        }
        
        #myDiagramDiv:hover {
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.1);
        }
        
        #myPaletteDiv:hover {
            box-shadow: 0 0 20px rgba(129, 140, 248, 0.1);
        }
    </style>
@endsection


@section('script')
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    
    <script src="{{ asset('js/flowchart.js') }}"></script>

    <script type="text/javascript" src="{{ asset('js/mxgraph/javascript/mxClient.js') }}"></script>

    <script id="code">
      function showHelp() {
          Swal.fire({
              title: 'Panduan',
              icon: 'info',
              html: `
                  <ul class="text-sm text-justify leading-relaxed">
                      <li>- Drag komponen dari panel kiri ke area gambar</li>
                      <li>- Hubungkan komponen dengan menarik garis</li>
                      <li>- Klik dua kali pada komponen untuk mengedit teks</li>
                      <li>- Tombol selesai digunakan untuk menyimpan jawaban</li>
                  </ul>
              `,
              confirmButtonText: 'Ok'
          });
      }

      setInterval(() => {
          if (typeof myDiagram !== 'undefined' && myDiagram.model.nodeDataArray.length > 0) {
              updateProgress();
              // Auto-save logic here
              console.log('Auto-saving...');
          }
      }, 30000); 

      const STORAGE_KEY = `flowchart_${{{ $data_soal->id }}}_${{{ Auth::user()->userable_type }}}_${{{ Auth::user()->userable->id }}}`;
      let autoSaveInterval;

      function saveToLocalStorage() {
        try {
            const modelData = myDiagram.model.toJson();
            const timestamp = new Date().toISOString();
            
            const saveData = {
                flowchart_data: modelData,
                timestamp: timestamp,
                soal_id: {{ $data_soal->id }},
                user_id: {{ Auth::user()->userable->id ?? 'null' }}
            };
            
            localStorage.setItem(STORAGE_KEY, JSON.stringify(saveData));
            console.log('Flowchart tersimpan ke localStorage pada:', timestamp);

        } catch (error) {
            console.error('Error menyimpan ke localStorage:', error);
        }
      }

      function loadFromLocalStorage() {
          try {
              const savedData = localStorage.getItem(STORAGE_KEY);
              
              if (savedData) {
                  const parsedData = JSON.parse(savedData);
                  
                  // Verifikasi data
                  if (parsedData.soal_id === {{ $data_soal->id }}) {
                      document.getElementById("mySavedModel").value = parsedData.flowchart_data;
                      
                      // Tampilkan notifikasi recovery
                      showRecoveryNotification(parsedData.timestamp);
                      
                      return true;
                  }
              }
          } catch (error) {
              console.error('Error memuat dari localStorage:', error);
          }
          
          return false;
      }

      function setupAutoSave() {
          autoSaveInterval = setInterval(() => {
              if (typeof myDiagram !== 'undefined' && myDiagram.model.nodeDataArray.length > 0) {
                  saveToLocalStorage();
              }
          }, 10000);
          
          myDiagram.addDiagramListener("Modified", function(e) {
              clearTimeout(window.saveTimeout);
              window.saveTimeout = setTimeout(() => {
                  saveToLocalStorage();
              }, 2000);
          });
          
          window.addEventListener('beforeunload', function(e) {
              if (typeof myDiagram !== 'undefined' && myDiagram.model.nodeDataArray.length > 0) {
                  saveToLocalStorage();
              }
          });
      }

      function clearLocalStorage() {
        try {
            localStorage.removeItem(STORAGE_KEY);
            console.log('Local storage cleared');
        } catch (error) {
            console.error('Error menghapus localStorage:', error);
        }
      }

      let allowedComponents = @json($pengaturanKomponen ?? []);

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
            // Cari bagian ini dalam kode Anda dan ganti dengan yang di bawah:
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
                // Tambahkan lebih banyak port untuk Decision (Diamond)
                makePort("T", go.Spot.Top, false, true),
                makePort("TL", go.Spot.TopLeft, true, true),      
                makePort("TR", go.Spot.TopRight, true, true),     
                makePort("L", go.Spot.Left, true, true),
                makePort("R", go.Spot.Right, true, true),
                makePort("BL", go.Spot.BottomLeft, true, true),   
                makePort("BR", go.Spot.BottomRight, true, true),  
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
                makePort("T", go.Spot.Top, true, true),           
                makePort("TR", go.Spot.TopRight, true, true),     
                makePort("R", go.Spot.Right, true, true),         
                makePort("BR", go.Spot.BottomRight, true, true),  
                makePort("B", go.Spot.Bottom, true, true),        
                makePort("BL", go.Spot.BottomLeft, true, true),   
                makePort("L", go.Spot.Left, true, true),          
                makePort("TL", go.Spot.TopLeft, true, true)       
            ));

            // replace the default Link template in the linkTemplateMap
            myDiagram.linkTemplate =
              $(go.Link,
                  {
                      routing: go.Link.AvoidsNodes,
                      curve: go.Link.JumpOver,
                      corner: 5, 
                      toShortLength: 4,
                      relinkableFrom: true,
                      relinkableTo: true,
                      reshapable: true,
                      resegmentable: true,
                      // PENTING: Binding yang eksplisit untuk port
                      fromPortId: "",
                      toPortId: "",
                      mouseEnter: function(e, link) { 
                          link.findObject("HIGHLIGHT").stroke = "rgba(30,144,255,0.2)"; 
                      },
                      mouseLeave: function(e, link) { 
                          link.findObject("HIGHLIGHT").stroke = "transparent"; 
                      }
                  },
                  // Binding eksplisit untuk mempertahankan port connections
                  new go.Binding("fromPortId", "fromPort"),
                  new go.Binding("toPortId", "toPort"),
                  new go.Binding("points").makeTwoWay(),
                  $(go.Shape,  // highlight shape
                      { isPanelMain: true, strokeWidth: 8, stroke: "transparent", name: "HIGHLIGHT" }),
                  $(go.Shape,  // link path shape
                      { isPanelMain: true, stroke: "gray", strokeWidth: 2 }),
                  $(go.Shape,  // arrowhead
                      { toArrow: "standard", stroke: null, fill: "gray"}),
                  $(go.Panel, "Auto",  // link label
                      { visible: false, name: "LABEL", segmentIndex: 2, segmentFraction: 0.5},
                      new go.Binding("visible", "visible").makeTwoWay(),
                      $(go.Shape, "RoundedRectangle",
                          { fill: "#F8F8F8", stroke: null }),
                      $(go.TextBlock, "Yes",
                          {
                              textAlign: "center",
                              font: "10pt helvetica, arial, sans-serif",
                              stroke: "#333333"
                          },
                          new go.Binding("text").makeTwoWay())
                  )
              );
            // Make link labels visible if coming out of a "conditional" node.
            // This listener is called by the "LinkDrawn" and "LinkRelinked" DiagramEvents.
            function showLinkLabel(e) {
              var label = e.subject.findObject("LABEL");
              if (label !== null) {
                  var fromNode = e.subject.fromNode;
                  
                  // Hanya tampilkan label untuk node Decision (Diamond)
                  if (fromNode.data.figure === "Diamond") {
                      label.visible = true;
                      
                      // Hitung jumlah link yang keluar dari node ini
                      var outgoingLinks = [];
                      fromNode.findLinksOutOf().each(function(link) {
                          outgoingLinks.push(link);
                      });
                      
                      // Tentukan teks berdasarkan urutan link
                      var linkIndex = outgoingLinks.indexOf(e.subject);
                      var labelText = "";
                      
                      if (linkIndex === 0) {
                          labelText = "Yes";
                      } else if (linkIndex === 1) {
                          labelText = "No";
                      } else {
                          labelText = null; 
                          label.visible = false;
                      }
                      
                      // Update teks label
                      myDiagram.model.setDataProperty(e.subject.data, "text", labelText);
                  } else {
                      label.visible = false;
                  }
              }
            }
            // temporary links used by LinkingTool and RelinkingTool are also orthogonal:
            // myDiagram.toolManager.linkingTool.temporaryLink.routing = go.Link.Orthogonal;
            // myDiagram.toolManager.relinkingTool.temporaryLink.routing = go.Link.Orthogonal;

            myDiagram.toolManager.linkingTool = new go.LinkingTool();
            myDiagram.toolManager.linkingTool.portGravity = 1; 
            myDiagram.toolManager.linkingTool.archetypeLinkData = { routing: go.Link.Orthogonal };

            myDiagram.toolManager.linkingTool.insertLink = function(fromnode, fromport, tonode, toport) {
                var newlink = go.LinkingTool.prototype.insertLink.call(this, fromnode, fromport, tonode, toport);
                
                // Pastikan link menggunakan port yang benar-benar dipilih user
                if (newlink !== null) {
                    myDiagram.model.setFromKeyForLinkData(newlink.data, fromnode.data.key);
                    myDiagram.model.setToKeyForLinkData(newlink.data, tonode.data.key);
                    myDiagram.model.setDataProperty(newlink.data, "fromPort", fromport.portId);
                    myDiagram.model.setDataProperty(newlink.data, "toPort", toport.portId);
                }
                
                return newlink;
            };

            load();  // load an initial diagram from some JSON text
            // initialize the Palette that is on the left side of the page
            const paletteNodeDataArray = [];

            if (allowedComponents.includes('Terminator')) {
                paletteNodeDataArray.push({ category: "Terminator", text: "Start/End"});
            }
            if (allowedComponents.includes('Process')) {
                paletteNodeDataArray.push({ category: "Process", text: "Process" });
            }
            if (allowedComponents.includes('Decision')) {
                paletteNodeDataArray.push({ category: "Decision", text: "Decision", figure: "Diamond" });
            }
            if (allowedComponents.includes('InputOutput')) {
                paletteNodeDataArray.push({ category: "InputOutput", text: "Input/Output" });
            }
            if (allowedComponents.includes('ManualOperation')) {
                paletteNodeDataArray.push({ category: "ManualOperation", text: "Manual Process" });
            }
            if (allowedComponents.includes('Comment')) {
                paletteNodeDataArray.push({ category: "Comment", text: "Comment" });
            }
            if (allowedComponents.includes('PredefinedProcess')) {
                paletteNodeDataArray.push({ category: "PredefinedProcess", text: "Predefined Process" });
            }
            if (allowedComponents.includes('Display')) {
                paletteNodeDataArray.push({ category: "Display", text: "Display" });
            }
            if (allowedComponents.includes('Preparation')) {
                paletteNodeDataArray.push({ category: "Preparation", text: "Preparation" });
            }
            if (allowedComponents.includes('OnPageReference')) {
                paletteNodeDataArray.push({ category: "OnPageReference", text: "" });
            }
            if (allowedComponents.includes('OffPageReference')) {
                paletteNodeDataArray.push({ category: "OffPageReference", text: "" });
            }

            myPalette =
              $(go.Palette, "myPaletteDiv",  // must name or refer to the DIV HTML element
                {
                  "animationManager.duration": 800, // slightly longer than default (600ms) animation
                  nodeTemplateMap: myDiagram.nodeTemplateMap,  // share the templates used by myDiagram
                  // model: new go.GraphLinksModel([  // specify the contents of the Palette
                  //   { category: "OnPageReference", text: "" },      // On-Page Reference
                  //   { category: "OffPageReference", text: "" },
                  //   { category: "Terminator", text: "Start/End"},
                  //   { text: "Process" },
                  //   { text: "Decision", figure: "Diamond" },
                  //   { category: "InputOutput", text: "Input/Output" },
                  //   { category: "ManualOperation", text: "Manual Process" },
                  //   { category: "Comment", text: "Comment" },
                  //   { category: "PredefinedProcess", text: "Predefined Process" },
                  //   { category: "Display", text: "Display" },
                  //   { category: "Preparation", text: "Preparation" }, 
                  // ])
                  model: new go.GraphLinksModel(paletteNodeDataArray)
                });
      }
      
      function showPorts(node, show) {
          var diagram = node.diagram;
          if (!diagram || diagram.isReadOnly || !diagram.allowLink) return;
          
          node.ports.each(function(port) {
              if (show) {
                  // Tampilkan port dengan visual yang lebih jelas
                  port.stroke = "white";
                  port.strokeWidth = 3;
                  port.fill = "rgba(255, 255, 255, 0.3)";
              } else {
                  port.stroke = null;
                  port.strokeWidth = 1;
                  port.fill = "transparent";
              }
          });
      }
      
      function save() {
          document.getElementById("mySavedModel").value = myDiagram.model.toJson();
          myDiagram.isModified = false;
      }
      
      function load() {
          const hasLocalData = loadFromLocalStorage();
    
          if (!hasLocalData) {
              // Jika tidak ada data lokal, gunakan default
              const defaultModel = document.getElementById("mySavedModel").value;
              if (!defaultModel || defaultModel.trim() === '') {
                  document.getElementById("mySavedModel").value = '{ "nodeDataArray": [], "linkDataArray": [] }';
              }
          }

          myDiagram.model = go.Model.fromJson(document.getElementById("mySavedModel").value);

          setupAutoSave();
      }
      
      // function makeSVG() {
      //     var svg = myDiagram.makeSvg({
      //       scale: 0.5
      //     });
      //     svg.style.border = "1px solid black";
      //     obj = document.getElementById("SVGArea");
      //     obj.appendChild(svg);
      //     if (obj.children.length > 0) {
      //       obj.replaceChild(svg, obj.children[0]);
      //     }
      // }
      
      // function downloadSVG() {
      //     var svg = myDiagram.makeSvg({
      //       scale: 1.0
      //     });
      //     var svgBlob = new Blob([svg.outerHTML], { type: "image/svg+xml" });
      //     var svgUrl = URL.createObjectURL(svgBlob);
      //     var link = document.createElement("a");
      //     link.href = svgUrl;
      //     link.download = "flowchart.svg";
      //     link.click();
      // }

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

      function showConfirmation(callback) {
          Swal.fire({
              title: 'Perhatian',
              text: 'Apakah jawaban yang anda buat sudah benar?',
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Lanjutkan',
              cancelButtonText: 'Batal',
              reverseButtons: true
          }).then((result) => {
              if (result.isConfirmed) {
                  callback();
              }
          });
      }  

      function saveFlowchartToDatabase() {
          save();
          
          const jsonText = document.getElementById('mySavedModel').value;
          
          try {
              JSON.parse(jsonText);
          } catch (e) {
              alert('Data flowchart tidak valid!');
              return;
          }
          
          if (!jsonText || jsonText.trim() === '') {
              alert('Tidak ada data flowchart untuk disimpan!');
              return;
          }

          const canvas = myDiagram.makeImageData({
              scale: 1,
              background: "white",
              type: "image/png"
          });
          
          const saveButton = event.target;
          const originalText = saveButton.innerHTML;
          saveButton.innerHTML = 'Menyimpan...';
          saveButton.disabled = true;
          
          const requestData = {
              soal_id: {{ $data_soal->id }},
              flowchart_data: jsonText,
              flowchart_image: canvas,
              encrypted_task: '{{ $encryptedTask }}'
          };
          
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
                  clearLocalStorage();

                  saveToLocalStorage();

                  showSuccessMessage('Flowchart berhasil disimpan! Mengalihkan...');
                  
                  setTimeout(() => {
                      window.location.href = '{{ route("detail-tasks", ["id" => $encryptedTask]) }}';
                  }, 1000);
              } else {
                  showErrorMessage('Gagal menyimpan: ' + (data.message || 'Terjadi kesalahan'));
                  saveButton.innerHTML = originalText;
                  saveButton.disabled = false;
              }
          })
          .catch(error => {
              console.error('Error:', error);
              showErrorMessage('Terjadi kesalahan saat menyimpan flowchart');
              saveButton.innerHTML = originalText;
              saveButton.disabled = false;
          });
      }
      
      $(function(){
          $('#sample').trigger('onload');
      });
    </script>
@endsection