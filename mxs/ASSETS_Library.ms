--global siteUrl = @"http://assets.visco.no/"
global siteUrl = @"http://svg-web-003/"
global iconFile = @"\\visco.local\data\Instal_Sync\scripts\scripts\img\favicon.ico"
global trustUrl = siteUrl + @"vault/mxs/mxsTrustUser.php?user=" + sysInfo.username
global forceRefresh

fn httpGet url =
(	
	t = ""
	url += "&time=" + (timeStamp() as string)
	try
	(
		w = dotNetObject (dotNetClass "System.Net.WebClient")
		s = dotNetClass "System.IO.StreamReader"
		o = w.OpenRead url
		l = dotNetObject s o
		t = l.ReadToEnd()
	)
	catch()
	
	return t
)

 fn useSettings k p w =
(
	u = sysInfo.username
	d = getFilenamePath  (getThisScriptFilename())
	n = filenameFromPath (getThisScriptFilename())			
	settingsFile = d + @"ini\" + u + "_" + n + ".ini"
		
	v = maxVersion()
	v = 2000 + ((v[1] / 1000) - 2)
	
	t = v as string
	
	case w of
	(
		"r":
		(
			s = getINISetting settingsFile t k
			case p of
			(
				"int": return s as integer
				"float": return s as float
				"bool": if s == "true" or s.count < 1 then true else false
				"bool_not": if s == "true" then true else false
				
				default: return s
			)
		)
		default: return setINISetting settingsFile t k (p as string)
	)
)	

global _ViscoAssetsForm
global _FILLREPORT = "\n1. Open full model info\n2. Go to comments\n3. Check the button \"Send As Bug Report\"\n3. Fill the form\n4. Press the button \"Send\""
global _MODELNOTEXIST = "This model not exist!\n\nPlease notify administrator about this issue:" + _FILLREPORT
global _MODELBAD = "This model not exist in data base!\n\nPlease notify administrator about this issue:" + _FILLREPORT

try(_ViscoAssetsForm.close())catch()	
	
_ViscoAssetsForm = dotNetObject "MaxCustomControls.MaxForm"
_ViscoAssetsForm.size = dotNetObject "System.Drawing.Size" 1240 800
_ViscoAssetsForm.MinimumSize = dotNetObject "System.Drawing.Size" 800 800
_ViscoAssetsForm.ShowInTaskBar = true
_ViscoAssetsForm.text = siteUrl
_ViscoAssetsForm.StartPosition = _ViscoAssetsForm.StartPosition.Manual	
	
executeTimer = dotNetObject "System.Windows.Forms.Timer"	
executeTimer.interval = 10

	
try(_ViscoAssetsForm.icon = dotNetObject "System.Drawing.Icon" iconFile)catch()
--_ViscoAssetsForm.topmost = true


wbTrust = dotNetObject "System.Windows.Forms.WebBrowser"
wbTrust.size =dotNetObject "System.Drawing.Size" 0 0
wbTrust.location = dotNetObject "System.Drawing.Point" -100 -100
wbTrust.url = dotNetObject "System.Uri" trustUrl

httpGet trustUrl	
sleep 0.2

execCommand = dotNetObject "System.Windows.Forms.Textbox"			
execCommand.size = dotNetObject "System.Drawing.Size" 0 0
execCommand.location = dotNetObject "System.Drawing.Point" -100 -100
	
global wb = dotNetObject "System.Windows.Forms.WebBrowser"
wb.size = _ViscoAssetsForm.ClientSize
--wb.AllowWebBrowserDrop  = true
wb.location = dotNetObject "System.Drawing.Point" 0 0
wb.url = dotNetObject "System.Uri" siteUrl
wb.ObjectForScripting = execCommand

	
_ViscoAssetsForm.controls.add  execCommand
_ViscoAssetsForm.controls.add  wbTrust
_ViscoAssetsForm.controls.add  wb

fn getWidnowState =
(
	s = dotNetClass "System.Windows.Forms.FormWindowState"
	
	case _ViscoAssetsForm.WindowState of
	(	
		(s.Maximized): return #maximized
		(s.Minimized): return #minimized
		default: return #normal
	)
)

fn setWindowState =
(	
	v = useSettings "STATE" "str" "r"
	
	case v of
	(
		"maximized": _ViscoAssetsForm.WindowState = (dotNetClass "System.Windows.Forms.FormWindowState").Maximized
		--"minimized": _ViscoAssetsForm.WindowState = s.Minimized
		default: _ViscoAssetsForm.WindowState = (dotNetClass "System.Windows.Forms.FormWindowState").Normal
	)
)

fn saveWindowState =
(
	s = getWidnowState()
	useSettings "STATE" s "w"
)

fn saveWindowPos =
(
	s = getWidnowState()
	
	if(s != #maximized) do
	(	
		x = _ViscoAssetsForm.left
		y = _ViscoAssetsForm.top
	
		if(x < -31000 or y < -31000) do return false
		
		useSettings "POSX" x "w"
		useSettings "POSY" y "w"
	) 	
)

fn saveWindowSize =
(
	s = getWidnowState()
	
	if(s == #normal) do
	(
		w = _ViscoAssetsForm.size.width
		h = _ViscoAssetsForm.size.height
				
		useSettings "WIDTH" w "w"
		useSettings "HEIGHT" h "w"
	)		
)

fn setWindowPos =
(
	x = useSettings "POSX" "int" "r"	
	y = useSettings "POSY" "int" "r"	
	
	if(x != 0 and y != 0) do	
	(
		_ViscoAssetsForm.Location = dotNetObject "System.Drawing.Point" x y
	)
)

fn setWindowSize =
(
	w = useSettings "WIDTH" "int" "r"	
	h = useSettings "HEIGHT" "int" "r"	
	
	
	if(w != 0 and h != 0) do	
	(
		_ViscoAssetsForm.size = dotNetObject "System.Drawing.Size" w h
	)
)

fn forceRefresh = 
(
	for i in 1 to 10 do
	(
		wb.refresh()
		wb.refresh (dotnetclass "System.Windows.Forms.WebBrowserRefreshOption").Completely
	)
)

fn goBack = wb.GoBack()
fn goForward = wb.GoForward()

fn onResize s e = 
(
	wb.size = _ViscoAssetsForm.ClientSize
	saveWindowSize()		
)

fn onMove s e = 
(
	saveWindowPos()
)

fn onClose s e =
(
	saveWindowSize()
	saveWindowPos()
	saveWindowState()
)

fn onLoad s e =
(
	setWindowPos()
	setWindowSize()
	setWindowState()
)

fn onNavigating e =
(
	enableAccelerators = false
)

fn onFocus e = 
(
	enableAccelerators = false
)

fn setTexturesPath path: "" geom: selection type: BitmapTex =
(
	for i in geom do
	(
		for b in getClassInstances type target: i do 
		(	
			if(b.filename != undefined and not doesFileExist b.filename) do
			(
				b.filename = path + b.filename
			)
		)
	)
)

fn setIesPath path: "" geom: selection type: light =
(
	for l in geom where superClassOf l == type do 
	(
		if(isProperty l #ies_file and not doesFileExist l.ies_file) do l.ies_file = path + l.ies_file
		if(isProperty l #webFile and not doesFileExist l.webFile) do l.webFile = path + l.webFile
		if(isProperty l #iesFile and not doesFileExist l.iesFile) do l.iesFile = path + l.iesFile	
	)
)

fn showStatusPaste t c: false =
(
	global statusText = t
	global timerActive = c
	global PasteStatus
	
	try(destroyDialog PasteStatus) catch()
	
	rollout PasteStatus "MastaMan" 
	(
		label lblStatus "Downloading..." offset: [0, 14] 
		timer tmrTimer "" active: true interval: 30000
		
		on CopyTo open do
		(
			lblStatus.caption = statusText
			tmrTimer.active = timerActive
		)
		
		on tmrTimer tick do 
		(	
			try(destroyDialog PasteStatus)catch()
			tmrTimer.active = false
		)
	)
	createDialog PasteStatus 160 50 style: #(#style_border)
)

fn addFile f mode: #merge =
(								
	if(not doesFileExist f) do return messageBox "Error: File not exist!" title: "Warning!"
	
	showStatusPaste "Downloading..."
	p = (getFilenamePath f)
		
	disableSceneRedraw()
	case mode of
	(
		#open:
		(
			setSaveRequired true
			checkForSave()
			resetMaxFile #noPrompt
			loadMaxFile f useFileUnits: true quiet:true	
			
			--setTexturesPath path: p geom: objects type: BitmapTex
			--try(setTexturesPath path: p geom: objects type: CoronaBitmap)catch()
			setIesPath path: p geom: objects			
		)
		#xref:
		(
			objs = getMAXFileObjectNames f
			o = xrefs.addnewXrefObject f objs dupMtlNameAction: #autoRename
			select o
		)
		default: 
		(
			mergeMaxFile f #select #promptDups  #promptMtlDups  quiet:true
	
			setTexturesPath path: p geom: selection type: BitmapTex
			try(setTexturesPath path: p geom: selection type: CoronaBitmap)catch()
			setIesPath path: p geom: selection
		)
	)
		
	toolMode.selectioncenter() 
	enableSceneRedraw()
	
	max tool zoomextents all
		
	try(destroyDialog PasteStatus) catch()
	
	return true
)

fn onExecute e =
(	
	executeTimer.start()
)

fn onExecuteCommand s e =
(
	executeTimer.stop()
	
	t = filterString execCommand.text "="
	p = if(t[2] != undefined) then (filterString t[2] "#")[1] else ""
	
	case t[1] of
	(
		"MERGE_MODEL": addFile p mode: #merge
		"XREF_MODEL": addFile p mode: #xref
		"OPEN_MODEL": addFile p mode: #open
		"MODELNOTEXIST": messageBox _MODELNOTEXIST title: "Error!"
		"MODELBAD": messageBox _MODELBAD title: "Error!"
		"GOBACK": goBack()
		"GOFORWARD": goForward()
		"FORCEREFRESH": forceRefresh()
	)
	
	execCommand.text = ""		
)

dotNet.addEventHandler _ViscoAssetsForm "Resize" onResize
dotNet.addEventHandler _ViscoAssetsForm "Move" onMove
dotNet.addEventHandler _ViscoAssetsForm "Load" onLoad
dotNet.addEventHandler _ViscoAssetsForm "Closing" onClose
dotnet.addEventHandler wb "gotFocus" onFocus
dotnet.addEventHandler wb "Click" onFocus
dotNet.addEventHandler wb "Navigating" onNavigating
dotNet.addEventHandler execCommand "TextChanged" onExecute
dotnet.addEventHandler executeTimer "tick" onExecuteCommand

dotNet.setLifetimeControl _ViscoAssetsForm #dotnet

fn maxHWND = 
(
	maxHandlePointer=(Windows.GetMAXHWND())	
	sysPointer = DotNetObject "System.IntPtr" maxHandlePointer
	maxHwnd = DotNetObject "MaxCustomControls.Win32HandleWrapper" sysPointer
)

maxHandlePointer = (Windows.GetMAXHWND())
sysPointer = DotNetObject "System.IntPtr" maxHandlePointer
_ViscoAssetsForm.fromHandle sysPointer

_ViscoAssetsForm.ShowInTaskbar = off
--_ViscoAssetsForm.showmodeless()	
_ViscoAssetsForm.show()
clearListener()

