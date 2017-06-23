struct _placer
(
	trackCallback = #continue,
	placeObj = undefined,
	originalScale,
	currentRotation,
	getOldPos = false,
	dnCursor = dotnetClass "Cursor",
	mousePoint = false,
	mousePos = mouse.pos.x,
	dnMousePos = dnCursor.Position,
	ignoreNormal = false,
	placeOn = #(),
	placeOnPlaced = false,
	placeObjPos = undefined,
	placeObjRot = undefined,
	
	fn handler s =
	(
		g = for i in s where i.parent == undefined collect i
		if(g[1] != undefined) do return g[1]
		if(superClassOf s[1] != GeometryClass) do return undefined
		return s[1]
	),
	
	fn rot mode: #cw =
	(
		placeObj = handler selection
		
		m = if(mode != #cw) then 1 else -1
		
		if(placeObj == undefined) do return #objectnone		
		in coordsys local rotate placeObj (eulerAngles 0 0 (90 * m))
		currentRotation = placeObj.rotation as eulerAngles	
		return #ok
	),
	
	fn track ir =
	(
		if(ir != undefined) then
		(											
			placeObj.pos = ir.pos 
			if(not ignoreNormal) do placeObj.dir = ir.dir
			
			r = placeObj.rotation as eulerAngles
			
			in coordsys local rotate placeObj (eulerAngles 0 0 (r.z - currentRotation.z))
					
			return #continue
		)
		else
		(			
			placeObj.pos = mapScreenToCP mouse.pos						
			placeObj.dir = [0,0,1]
			
			r = placeObj.rotation as eulerAngles			
			in coordsys local rotate placeObj (eulerAngles 0 0 (r.z - currentRotation.z))
		
			return #continue
		)
	),
	fn mouseTrackCallback msg ir obj faceNum shift ctrl alt =
	(
		case msg of
		(
			#freeMove:
			(																										
				if(shift and ctrl) then	(						 					
					p = mouse.pos.x						
					a = (p - mousePos)									
					mousePos = p
					
					in coordsys local rotate placeObj (angleaxis a [0,0,1])																				
					currentRotation = placeObj.rotation as eulerAngles				
					setSysCur #rotate
					getOldPos = true				
				) else
				(
					if(getOldPos) do dnCursor.Position = dnMousePos 
					getOldPos = false
					dnMousePos = dnCursor.Position
					mousePos = mouse.pos.x
					track ir	
				)
						
				return #continue
			)
			#mouseAbort:
			(																									
				if(mousePoint == true) then
				(	
					try(delete placeObj)catch()
				) else
				(					
					placeObj.rotation = placeObjRot
					placeObj.pos = placeObjPos
				)
				return #exit
				return #end
			)
			#mousePoint:
			(
				if(not alt and not ctrl and not shift) do
				(					
					maxops.cloneNodes placeObj clonetype:#instance actualNodelist:placeObj newnodes:&n					
					selectMore n
					
					join placeOn n
										
					mousePoint = true										
					if(placeOnPlaced) then return #end
					return #continue
				) 
				
				if(alt) do
				(
					in coordsys local rotate placeObj (angleaxis 90 [0,0,1])	
					currentRotation = placeObj.rotation as eulerAngles
				)
						
				
				return #continue
			)
			#mouseMove:
			(																	
				return #continue
			)									
		)
	),
	fn place =
	(				
		ignoreNormal = ignorenormal
		
		placeObj = handler selection
			
		if(placeObj == undefined) do 
		(
			trackCallback = #end
			return #objectnone
		)
		
		currentRotation = placeObj.rotation as eulerAngles
					
		s = selection as array		
		placeOn = for i in geometry where i.isHidden == false and findItem s i == 0 collect i
	
		placeObjPos = placeObj.pos
		placeObjRot = placeObj.rotation
		while (trackCallback != #exit) do (trackCallback = mouseTrack on: placeOn trackCallBack: mouseTrackCallback)
		return #ok
	)	
)

try(destroyDialog _rObjectPlacer)catch()

global __AUTO_PLACE_MODE = if(__AUTO_PLACE_MODE == undefined or __AUTO_PLACE_MODE == false) then false else true

rollout _rObjectPlacer "Object Placer"
(	
	imgTag tagPlace "" style: #bmp_center height: 36 width: 36 pos: [1, 1] tooltip: "Place selected object."
	imgTag tagRotateCCW "" style: #bmp_center height: 36 width: 36 pos: [46, 1] tooltip: "Rotate CCW."
	imgTag tagRotateCW "" style: #bmp_center height: 36 width: 36 pos: [83, 1] tooltip: "Rotate CW."
	dotnetcontrol tooltipPlaceHolder "System.Windows.Forms.Button" height:0 width: 0 align: #right pos: [260, 35]
	checkbox cbxIgnoreNormal "Place Vertical" pos: [135, 2]
	checkbox cbxPlaceOnPlaced "Place On Placed" pos: [135, 18]
	
	dotNetControl edtMiniManual "System.Windows.Forms.TextBox" width: 288  height: 62 pos: [0, 37]
	local initText = "Hold SHIFT+CTRL and move mouse for free rotation.\r\n\r\nALT+CLICK for rotate by 90deg the clock-wise.\r\n"
	local _PLACING = false
	local globalPlacer = undefined
	
	fn showTooltip c t title: "Warning!" show: 4000=
	(
		toolTip = dotnetobject "System.Windows.Forms.ToolTip"
		toolTip.SetToolTip c t   
		--toolTip.Active = true    
		toolTip.isBalloon = true
		toolTip.InitialDelay = 100
		toolTip.AutoPopDelay = 500
		toolTip.ReshowDelay = 50
		toolTip.ToolTipTitle = title
		toolTip.Show t c show	
		case title of
		(
			"Warning!": toolTip.ToolTipIcon = (dotnetclass "System.Windows.Forms.ToolTipIcon").Warning
			"Info!": toolTip.ToolTipIcon = (dotnetclass "System.Windows.Forms.ToolTipIcon").Info
			"Error!": toolTip.ToolTipIcon = (dotnetclass "System.Windows.Forms.ToolTipIcon").Error
		)	
	)
	
	fn imgButton t n  state: #out =
	(
		p = getFileNamePath (getThisScriptFileName())
					
		t.opacity = 0.0	
		case state of
		(
			#click: img = n + "-disabled.png"
			#in:  img = n + "-hover.png"
			default: img = n + ".png"
		)
		
		b = openBitmap (p + @"\img\" + img)
		t.bitmap = b
	)
	
	fn placeModel =
	(
		imgButton tagPlace "place" state: #in
		
		undo "Place Object" on
		(
			tooltipPlaceHolder.pos.x = tagPlace.pos.x + 32		
			edtMiniManual.text = initText
					
			_PLACING = true
			cbxIgnoreNormal.enabled = false
			cbxPlaceOnPlaced.enabled = false
			
			i = cbxIgnoreNormal.checked
			pp = cbxPlaceOnPlaced.checked
		 		 
			globalPlacer = _placer()
			globalPlacer.ignoreNormal = i
			globalPlacer.placeOnPlaced = pp
			r = globalPlacer.place()
		 
		 
			_PLACING = false
			cbxIgnoreNormal.enabled = true
			cbxPlaceOnPlaced.enabled = true
			
			if(r == #objectnone) do 
			(			
				showTooltip tooltipPlaceHolder "Please select object!" title: "Warning!"
				return false
			)
		)
		__AUTO_PLACE_MODE = false
		imgButton tagPlace "place"
	)
	
	on _rObjectPlacer open do
	(
		dialogPos = getDialogPos _rObjectPlacer
		setDialogPos _rObjectPlacer [dialogPos.x, 80]
		
		imgButton tagPlace "place"		
		imgButton tagRotateCW "rotate-cw"	
		imgButton tagRotateCCW "rotate-ccw"	
	
		edtMiniManual.MultiLine = true
		edtMiniManual.scrollbars = (dotnetclass "System.Windows.Forms.ScrollBars").vertical		
		edtMiniManual.acceptsReturn = true
		edtMiniManual.acceptsTab = true
		edtMiniManual.wordWrap = true
		edtMiniManual.readOnly = true
		
		edtMiniManual.text =  initText
		
		globalPlacer = _placer()
		if(__AUTO_PLACE_MODE == true) do placeModel()	
	)
	
	fn rotateObject mode: #cw=
	(		
		r = globalPlacer.rot mode: mode
		
		if(r == #objectnone) do 
		(
			showTooltip tooltipPlaceHolder "Please select object!" title: "Warning!"
			return false
		)
	)
	
	on tagPlace mouseover do imgButton tagPlace "place" state: #in
	on tagPlace mouseout do if(not _PLACING) do imgButton tagPlace "place" 
	on tagPlace mousedown do imgButton tagPlace "place" state: #click
	on tagPlace mouseup do imgButton tagPlace "place" state: #in
		
	on tagRotateCCW mouseover do imgButton tagRotateCCW "rotate-ccw" state: #in
	on tagRotateCCW mouseout do imgButton tagRotateCCW "rotate-ccw" 
	on tagRotateCCW mousedown do imgButton tagRotateCCW "rotate-ccw" state: #click
	on tagRotateCCW mouseup do imgButton tagRotateCCW "rotate-ccw" state: #in
		
	on tagRotateCW mouseover do imgButton tagRotateCW "rotate-cw" state: #in
	on tagRotateCW mouseout do imgButton tagRotateCW "rotate-cw" 
	on tagRotateCW mousedown do imgButton tagRotateCW "rotate-cw" state: #click
	on tagRotateCW mouseup do imgButton tagRotateCW "rotate-cw" state: #in
	
	on tagRotateCW click do 
	(	
		tooltipPlaceHolder.pos.x = tagRotateCW.pos.x + 32	
		rotateObject mode: #cw
	)
	on tagRotateCCW click do 
	(	
		tooltipPlaceHolder.pos.x = tagRotateCCW.pos.x + 32	
		rotateObject mode: #ccw
	)
	
	on tagPlace click do placeModel()	
)

createDialog _rObjectPlacer 270 100