global outputMap = compositeTextureMap()

(
	fn removeCallbacks = 
	(
		callbacks.removeScripts id:#renderChange
		callbacks.removeScripts id:#postNew
		callbacks.removeScripts id:#postReset		
		callbacks.removeScripts id:#preRender		
		callbacks.removescripts id:#selectionSetChanged
		callbacks.removeScripts id:#postOpen
	)
		
	removeCallbacks()
	try(closeRolloutFloater rAssetsLibraryModelPack)catch()
	try(closeRolloutFloater fFastRender) catch()
	
	fn getRender =
	(
		r = renderers.current as string

		if matchpattern r pattern:"*Corona*" do return #Corona
		if matchpattern r pattern:"*V_Ray_Adv*" do return #VRay
		if matchpattern r pattern:"*Default_Scanline*" do return #Scanline
		return #unknown
	)
	
	fn mapWorldToScreen cam pos =
	(	
		tm = cam.transform	
		fov = cam.fov
		
		aspect  = -renderHeight as float/ renderWidth

		posCS = pos * inverse tm

		tanX = tan (fov / 2)
		tanY = aspect * tanX
		depth = abs posCS.z
		bounds_X = tanX * depth 
		bounds_Y = tanY * depth

		nx = ((posCS.x / bounds_X)+1) / 2
		ny = ((posCS.y / bounds_Y)+1) / 2

		screen_X = nx * renderwidth
		screen_Y = ny * renderHeight

		return [screen_X, screen_y, 0]
	)	

	fn getCameraViewportID = 
	(
		for i in 1 to viewport.numViews where viewport.getCamera index: i != undefined do return i
		return undefined
	)

	fn getRenderElementsNum = (
		re = maxOps.GetCurRenderElementMgr()
		return re.NumRenderElements()
	)
		
	
	fn setSelectionRegion cam: undefined = 
	(	
		if(cam == undefined) do return false
		
		viewport.setCamera cam
		view = viewport.activeViewport
		
		renderSceneDialog.close()	
		setRenderType  #blowUp		
		renderSceneDialog.commit()
		renderSceneDialog.update()
			
		nodes = for node in selection where iskindof node GeometryClass collect node
		if nodes.count > 0 do
		(
			local bmin = [1e9,1e9,0], bmax = [-1e9,-1e9,0]
			
			for node in nodes do
			(
				mesh = snapshotasmesh node
				
				for v=1 to mesh.numverts do
				(
					vp = mapWorldToScreen cam (GetVert mesh v)
				
					if vp.x < bmin.x do bmin.x = vp.x
					if vp.x > bmax.x do bmax.x = vp.x
					if vp.y < bmin.y do bmin.y = vp.y
					if vp.y > bmax.y do bmax.y = vp.y
				)
				free mesh
			)
			w = (bmax.x - bmin.x) as integer
			h = (bmax.y - bmin.y) as integer

			padding = 10
			
			kw = 0
			kh = 0
			
			if(w > h) then kh = (w - h) / 2 else kw = (h - w) / 2 
			
			x = bmin.x - padding - kw
			y =  bmin.y - padding - kh
			w = w + (padding * 2) + (kw * 2)
			h = h + (padding * 2) + (kh * 2)
			
			rect = box2 x y w h

			if(not EditRenderRegion.IsEditing) do EditRenderRegion.EditRegion() 		
			viewport.setBlowupRect  view rect		
			EditRenderRegion.UpdateRegion()
		)
	)
		
	fn getMaxVersion = 
	(
		v = maxVersion()
		v = 2000 + ((v[1] / 1000) - 2)
		
		return v as string
	)

	fn useSettings k p w =
	(
		u = sysInfo.username
		d = getFilenamePath  (getThisScriptFilename())
		n = "ASSETS_Make Preview.ms"		
		settingsFile = d + @"ini\" + u + "_" + n + ".ini"
			
		t = getMaxVersion()
		
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
	
	fn clearCache =
	(
		f = @"c:\temp\make-preview\"
		d = getFiles (f + "*")
		for i in d do deleteFile i
	)
	
	fn getVFB c: 0 = 
	(	
		f = @"c:\temp\make-preview\"
		makeDir f
		
		r = getRender()
		b = undefined
		
		case r of 
		(
			#Corona: b = CoronaRenderer.CoronaFp.getVfbContent c true true
			#VRay: b = vrayVFBGetChannelBitmap c 
		)
				
		nb = bitmap renderWidth renderHeight color: white 
		copy b nb
		
		t = timeStamp()
		nb.filename = f + t as string  + ".jpg"
		
		save nb gamma: 2.2
		close nb
		
		bt = Bitmaptexture()	
		bt.bitmap = nb
			
		return  bt
	)
	
	fn setIniPostEffect s = (
		p = #()
		
		p[1] = useSettings ("BLENDMODE" + s as string) "string" "r"
		p[2] = useSettings ("OPACITY" + s as string) "string" "r"
		p[3] = useSettings ("EFFECT" + s as string) "string" "r"
		
		p[1] = if(p[1] != "") then  (p[1] as integer) - 1 else -1
		p[2]  = if(p[2] != "") then  (p[2] as integer) else -1
		p[3]  = if(p[3] == "" or p[3] == "true") then  true else false
		
		if(p[1] != -1) do outputMap.blendMode[s] = p[1] 
		if(p[2] != -1) do outputMap.opacity[s]  = p[2] 
		if(p[3] == "false") do outputMap.mapEnabled[s] = false
	)
	
		fn postProcessCorona =
	(
		vfbAlpha = getVFB c:1
		main = getVFB c:0
			
		
		-- Beauty / None
		--outputMap = compositeTextureMap()
		l = 1
		outputMap.mapList[l] = main
		outputMap.mapList[l].coords.blur = 0.01
		outputMap.mapList[l].name = "Beauty"
		

		-- Direct Light / SoftLight
		l = 2		
		outputMap.mapList[l] = getVFB c: l
		outputMap.mapList[l].coords.blur = 0.01
		outputMap.blendMode[l] = 15
		outputMap.opacity[l] = 15
		outputMap.mask[l] = vfbAlpha
		outputMap.mapList[l].name = "Direct Light"
		setIniPostEffect l

		-- Reflect / Screen
		l = 3		
		outputMap.mapList[l] = ColorCorrection()
		outputMap.mapList[l].map = getVFB c: l			
		outputMap.mapList[l].map.coords.blur = 0.01
		--outputMap.mapList[l].lightnessMode = 1
		--outputMap.mapList[l].liftRGB = -0.35			
		outputMap.blendMode[l] = 9
		outputMap.opacity[l] = 45
		outputMap.mask[l] = vfbAlpha
		outputMap.mapList[l].name = "Reflect"
		setIniPostEffect l

		-- Refract / Screen
		l = 4		
		outputMap.mapList[l] = getVFB c: l
		outputMap.mapList[l].coords.blur = 0.01
		outputMap.blendMode[l] = 9
		outputMap.opacity[l] = 50
		outputMap.mask[l] = vfbAlpha
		outputMap.mapList[l].name = "Refract"
		setIniPostEffect l
		
		-- Albedo / Overlay
		l = 5		
		outputMap.mapList[l] = getVFB c: l
		outputMap.mapList[l].coords.blur = 0.01
		outputMap.blendMode[l] = 14
		outputMap.opacity[l] = 25
		outputMap.mask[l] = vfbAlpha
		outputMap.mapList[l].name = "Albedo"
		setIniPostEffect l

		-- AO / Multiply
		l = 6		
		outputMap.mapList[l] = getVFB c: l
		outputMap.mapList[l].coords.blur = 0.01
		outputMap.blendMode[l] = 5
		outputMap.opacity[l] = 25
		outputMap.mask[l] = vfbAlpha
		outputMap.mapList[l].name = "AO"
		setIniPostEffect l
		
		-- Wire / Screen
		l = 7
		outputMap.mapList[l] = getVFB c: l
		outputMap.mapList[l].coords.blur = 0.01
		outputMap.blendMode[l] = 9
		outputMap.opacity[l] = 0
		outputMap.mask[l] = vfbAlpha
		outputMap.mapList[l].name = "Wire"
	)
	
	fn postProcessVRay =
	(
		vfbAlpha = getVFB c:2
		main = getVFB c:1
					
		-- Beauty / None
		--outputMap = compositeTextureMap()
		l = 1
		outputMap.mapList[l] = main
		outputMap.mapList[l].coords.blur = 0.01
		outputMap.mapList[l].name = "RGB color"
		

		-- Direct Light / SoftLight
		l = 2		
		outputMap.mapList[l] = getVFB c: 3
		outputMap.mapList[l].coords.blur = 0.01
		outputMap.blendMode[l] = 15
		outputMap.opacity[l] = 55
		outputMap.mask[l] = vfbAlpha
		outputMap.mapList[l].name = "Global Illumination"		
		setIniPostEffect l

		-- Reflect / Screen
		l = 3		
		outputMap.mapList[l] = ColorCorrection()
		outputMap.mapList[l].map = getVFB c: 4		
		outputMap.mapList[l].map.coords.blur = 0.01
		--outputMap.mapList[l].lightnessMode = 1
		--outputMap.mapList[l].liftRGB = -0.35			
		outputMap.blendMode[l] = 9
		outputMap.opacity[l] = 60
		outputMap.mask[l] = vfbAlpha
		outputMap.mapList[l].name = "Reflect"
		setIniPostEffect l

		-- Refract / Screen
		l = 4		
		outputMap.mapList[l] = getVFB c: 5
		outputMap.mapList[l].coords.blur = 0.01
		outputMap.blendMode[l] = 9
		outputMap.opacity[l] = 50
		outputMap.mask[l] = vfbAlpha
		outputMap.mapList[l].name = "Refract"
		setIniPostEffect l


		-- AO / Multiply
		l = 5		
		outputMap.mapList[l] = getVFB c: 6
		outputMap.mapList[l].coords.blur = 0.01
		outputMap.blendMode[l] = 5
		outputMap.opacity[l] = 25
		outputMap.mask[l] = vfbAlpha
		outputMap.mapList[l].name = "AO"
		setIniPostEffect l
		
		-- Wire / Screen
		l = 6	
		outputMap.mapList[l] = getVFB c: 7
		outputMap.mapList[l].coords.blur = 0.01
		outputMap.blendMode[l] = 9
		outputMap.opacity[l] = 0
		--outputMap.mask[l] = vfbAlpha
		outputMap.mapList[l].name = "Wire"
	)
	
	fn preRenderImageFromMap = renderMap outputMap size:[renderWidth, renderHeight] filter: false scale: 1.0 gamma: 2.2
	
	fn saveBitmap b f = (
		b.filename = f
		save b gamma: 2.2
		close b
	)
	
	fn batchRender = (	
				
		tmp = @"c:\temp\_tmpMakePreview.max"
		resetMaxFile #noPrompt
		loadMaxFile tmp useFileUnits: true quiet: true
		
		useSmartZoom = useSettings "BATCH_SMART_ZOOM" "bool" "r"
		usePostProcess = useSettings "BATCH_POST_PROCESS" "bool_not" "r"
		useSaveWire = useSettings "BATCH_SAVE_WIRE" "bool" "r"
		
		
		_BATCH_CAMERA = cameras[1]
		if(_BATCH_CAMERA == undefined) do return messageBox "Camera not found!" title: "Error!"
		
		_BATCH_CAMERA_TM = _BATCH_CAMERA.transform
		_BATCH_SAVE_PATH = useSettings "BATCH_SAVE_PATH" "str" "r"
		
		if(_BATCH_SAVE_PATH == "") do return messageBox "Please choose path!" title: "Error!"
		
		progressStart "Batch Render"
		escapeEnable = true

		for i in 1 to selectionSets.count do
		(	
			if(getProgressCancel() == true) do exit	
			
			outputFile = _BATCH_SAVE_PATH + @"\" + (getNamedSelSetName i) + ".jpg"
			outputFileWire = _BATCH_SAVE_PATH + @"\" + (getNamedSelSetName i) + "_wire.jpg"
						
			select selectionSets[i]
			unhide selectionSets[i]
			
			type = #view
			_BATCH_CAMERA.transform = _BATCH_CAMERA_TM
			
			if(useSmartZoom) do (
				if(selection.count == 0) do select geometry								
				viewport.setType #view_persp_user
				max zoomext sel		
				_BATCH_CAMERA.transform = (inverse(viewport.getTM()))
				setSelectionRegion cam: _BATCH_CAMERA
				type = #blowUp
			)
			
			clearCache()
			
			max quick render
			
			_BATCH_CAMERA.transform = _BATCH_CAMERA_TM
						
			hide selectionSets[i]
			
			r = getRender()
			
			if(usePostProcess) then (				
				case r of
				(
					#Corona: postProcessCorona()
					#VRay: postProcessVRay()
				)	
			) else (
				bb = getVFB c: 1
				outputMap.mapList[1] = bb
				outputMap.opacity[1] = 100
				outputMap.blendMode[1] = 0
			)
			
			bt = preRenderImageFromMap()			
			saveBitmap bt outputFile
			
			if(useSaveWire) do (
				-- Wire
				nn = getRenderElementsNum()
				if(nn > 1) do (
					bb = getVFB c: (nn + 2)
					c = outputMap.mapList.count
					outputMap.mapList[c] = bb
					outputMap.mapList[c].output.invert =  true
					outputMap.opacity[c] = 100
					outputMap.blendMode[c] = 0
					bt = preRenderImageFromMap()	
					saveBitmap bt outputFileWire 
				)
			)
						
			sleep 0.1			
			progressUpdate (100 * i / selectionSets.count)
			sleep 0.1
		)

		progressEnd()
		
		q = queryBox "Render complete!\n\nDo you want to open destination folder?" title: "Success!"
		if(q) do shellLaunch _BATCH_SAVE_PATH ""
	)


	batchRender()
)