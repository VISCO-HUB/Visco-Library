try(destroyDialog rViscoCatalog)catch()
rollout rViscoCatalog "VISCO | Catalog" width:650 height:600
(
	dotNetControl wb "System.Windows.Forms.WebBrowser" pos:[0,0] width:650 height:600
	dotNetControl execCommand "System.Windows.Forms.Textbox" pos:[0,0] visible:false

	global siteUrl = @"http://lib/index.html"
	global siteUrl = @"http://www.autodesk.com"
	global siteUrl = @"http://193.84.22.46:8282/3DLibrary"
	--global siteUrl = @"http://kitpes.org"
	--global siteUrl = @"http://getbootstrap.com/"
		
	-- Placer Globals
	global trackCallback = #continue
	global placeObj = #()
	global initMousePos
	global rotationAngle = 0
	global originalScale
	global currentRotation
	global shiftState = false

	fn stripSpecialChars s =
	(
		s = filterString s "%"
		t = ""
		for i in s do t += (trimLeft i "5C") + "\\"
		t = trimRight t "\\"
					
		return t
	)
	
	fn addFile f =
	(
		if(doesFileExist f) do mergeMaxFile f #select #mergeDups  #renameMtlDups  quiet:true 
	)
	
	fn getHandler s =
	(
		g = for i in s collect i.parent
		if(g[1] != undefined) do return g[1]
		if(superClassOf s[1] != GeometryClass) do return undefined
		return s[1]
	)
	
	fn DownloadFile url newFile = 
	(
		wc = dotNetClass "System.Net.WebClient"
		sr = dotNetClass "System.IO.StreamReader"
		wcob j =(dotNetObject wc)
		stm = wcobj.DownloadFile url newFile
	)
	
	fn setRot r rAngle: 2 =
	(
		return case of
		(
			(r < 0): -rAngle
			(r > 0): rAngle
			default: 0
		)
	)
	
	fn trackRay ir =
	(
		if(ir != undefined) then
		(											
			placeObj.pos = ir.pos 
			placeObj.dir = ir.dir
			
			r = placeObj.rotation as eulerAngles
			
			in coordsys local rotate placeObj (eulerAngles 0 0 (r.z - currentRotation.z))
		
			initMousePos = mouse.pos
			
			return #continue
		)
		else
		(			
			placeObj.pos = mapScreenToCP mouse.pos						
			placeObj.dir = [0,0,1]
			
			r = placeObj.rotation as eulerAngles			
			in coordsys local rotate placeObj (eulerAngles 0 0 (r.z - currentRotation.z))
			
			initMousePos = mouse.pos
			
			return #continue
		)
	)
	
	fn placeObject =
	(
		fn mouseTrackCallback msg ir obj faceNum shift ctrl alt =
		(	
			case msg of
			(
				#freeMove:
				(			
					initMousePos = mouse.pos					
																	
					return #continue
				)
				#mouseAbort:
				(																				
					currentRotation = undefined
					shiftState = false
										
					return #end
				)
				#mousePoint:
				(
					originalScale = placeObj.scale									
					shiftState = false
					
					return #continue
				)
				#mouseMove:
				(									
					if(shift and shiftState == false) then
					(												
						maxops.cloneNodes placeObj clonetype:#instance actualNodelist:placeObj newnodes:n

						shiftState = true
					)
					
					if(ctrl) do
					(
						s = ((initMousePos.y  - mouse.pos.y) / 100.0) + originalScale
						
						if s != undefined and (s[1] > 0.0 and s[2] > 0.0 and s[3] > 0.0) do placeObj.scale = s	
						
						return #continue
					)	
					
					if(alt) do
					(
						p = rotationAngle				
						rotationAngle = (initMousePos.x  - mouse.pos.x)
						rot = p - rotationAngle
							
						rot = setRot rot								
										
						in coordsys local rotate placeObj (angleaxis rot [0,0,1])								
						
						currentRotation = placeObj.rotation as eulerAngles
	
						return #continue	
					)
					
					trackRay ir
					return #continue
				)									
			)
		)
		
		placeObj = getHandler selection
			
		if(placeObj == undefined) do 
		(
			messageBox "Please select object!" title: "Warning!"
			return trackCallback = #end
		)
		
		originalScale = placeObj.scale
		currentRotation = placeObj.rotation as eulerAngles
		
		o = for i in geometry where i.isHidden == false and placeObj != i  collect i
		trackCallback = mouseTrack on:o trackCallBack: mouseTrackCallback
	)
	
	on rViscoCatalog open do
	(
		wb.url = dotNetObject "System.Uri" siteUrl
		wb.AllowWebBrowserDrop  = true
		wb.ObjectForScripting = execCommand;
		
		
		DownloadFile 
	)

	on execCommand TextChanged e do
	(
		t = filterString execCommand.text "="
		
		case t[1] of
		(
			"add_model": addFile t[2]
		)
		
		execCommand.text = ""
		setFocus rViscoCatalog.wb
	)
	
	on wb DocumentCompleted t do
	(		
		u = wb.url.Query
		u = stripSpecialChars u
		p = filterString u "="
	  		
		case p[1] of
		(
			"?add_model": addFile p[2]
			"?place_model": 
			(
				while trackCallback != #end do placeObject()
				trackCallback = #continue
			)
			"?add_model_and_place": 
			(
				if(doesFileExist p[2]) do 
				(
					addFile p[2]
					
					while trackCallback != #end do placeObject()
					trackCallback = #continue
				)
			)
			"?close": 
			(
				DestroyDialog rViscoCatalog
			)
		)

		setFocus rViscoCatalog.wb				
	)	
	
	on rViscoCatalog resized s do
	(
		rViscoCatalog.wb.size = dotnetobject "System.Drawing.Size" s[1] s[2];
	)
	
	on rViscoCatalog mouseMove a do
	(
		setFocus rViscoCatalog.wb
	)
	
	on rViscoCatalog okToClose do
	(
		wb.Dispose() 
	)
)
createdialog 
CreateDialog rViscoCatalog 650 600 style:#(#style_titlebar,#style_sysmenu,#style_toolwindow,#style_resizing)
