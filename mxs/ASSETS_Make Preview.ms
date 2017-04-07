/*  
[INFO] 

NAME = Make Preview
VERSION = 1.0.5
AUTHOR = Vasily Lukyanenko
DEV = VISCO
HELP = 

[INSTALL]

MACRO=MakePreview
CAT=VISCO
TEXT=Make Preview
ICON=#("PhysX_Main", 3)

[1.0.0]

* First release =

[1.0.2]

+ Added: VRay support=
- BugFix: Script not work when reload scene=

[1.0.3]

+ Added: Display wire in Editor=

[1.0.4]

- BugFix: Show/Hide All effect in Editor=

[1.0.5]

* Changed: Improve Wire Parameters in Editor=
+ Added: Final rende size options=
+ Added: Confirm message on Setup Scene=
+ Added: Disable steps when scene not setup=
+ Added: Choose renderer on Setup Scene=

[ABOUT]

This script helps to quickly make previews=
for Assets Library=

[TEST]

[SCRIPT]
*/	


try(closeRolloutFloater fFastRender) catch()
global fFastRender = newRolloutFloater "Make Preview" 250 800

global lightHDR = undefined
global reflectHDR = undefined
global renderSystem = undefined
global outputMap = compositeTextureMap()

global _rStep0
global _rStep1
global _rStep2
global _rStep3
global _rPreset

global 	blendOptions = #(
	"Normal",
	"Average",
	"Addition",
	"Subtract",
	"Darken",
	"Multiply",
	"Color Burn",
	"Linear Burn",
	"Lighten",
	"Screen",
	"Color Dodge",
	"Linear Dodge",
	"Spotlight",
	"Spotlight Blend",
	"Overlay",
	"Soft Light",
	"Hard Light",
	"Pin Light",
	"Hard Mix",
	"Difference",
	"Exclusion",
	"Hue",
	"Saturation",
	"Color",
	"Value"
 ) 

fn getScriptInfo s releaseInfo: "" =
(		
	if(releaseInfo != "") do
	(
		r = getINISetting s releaseInfo
		return r
	)
	
	v = getINISetting s "INFO" "VERSION"
	a = getINISetting s "INFO" "AUTHOR"
	n = getINISetting s "INFO" "NAME"
	o = getINISetting s "ABOUT"
	c = getINISetting s "INFO" "DEV"
	h = getINISetting s "INFO" "HELP"
	
	r = for i in (getINISetting s) where i != "ABOUT" and i != "SCRIPT" and i != "COPY" collect i
	
	return #(n, a, v, o, r, c, h)
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
	n = filenameFromPath (getThisScriptFilename())			
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
	 
fn getRender =
(
	r = renderers.current as string

	if matchpattern r pattern:"*Corona*" do return #Corona
	if matchpattern r pattern:"*V_Ray_Adv*" do return #VRay
	if matchpattern r pattern:"*Default_Scanline*" do return #Scanline
	return #unknown
)

fn findTheRender s =
(
	r = for o in RendererClass.classes where (matchPattern (o as string) pattern: ("*" + s + "*")) collect  o
	if(r.count == 1)	do return r[1]
	return false	
)	

fn setupRenderEngine =
(
	try(destroyDialog  _rChooserRenderEngine) catch()
	rollout _rChooserRenderEngine "Choose Renderer"
	(
		listbox lbxRenderTypes "" items: #() width: 200 offset: [-13, -5]
		button btnSelectRenderOk "Ok" across: 2 width: 60 height: 25 align: #left offset: [0, -2]
		button btnCloseThisDialog "Close" width: 60 height: 25 align: #right offset: [0, -2]
		
		global renderEngine = #()
		global renderEngineSelect = #none
		
		on _rChooserRenderEngine open do
		(
			renderEngineSelect = #none
			renderSceneDialog.close()
			
			itm = #()
						
			r = findTheRender "v_ray_adv"
			if(r != false) do 
			(
				append itm "V-Ray Adv"
				append renderEngine r
			)
			
			r = findTheRender "corona"
			if(r != false) do 
			(
				append itm "Corona"
				append renderEngine r
			)
			
			r = findTheRender "scanline"
			if(r != false) do 
			(
				append itm "Default Scanline"
				append renderEngine r
			)
			
			lbxRenderTypes.items = itm
		)
		
		on btnCloseThisDialog pressed do 
		(				
			try(destroyDialog  _rChooserRenderEngine) catch()
		)
		
		on btnSelectRenderOk pressed do
		(
			i = lbxRenderTypes.selection
			renderers.current = execute(renderEngine[i] as string + "()")
			renderSystem = renderers.current 
			renderEngineSelect = #selected
			
			try(destroyDialog  _rChooserRenderEngine) catch()
		)
	)

	createDialog _rChooserRenderEngine 200 170 modal: true parent: _rStep0.hwnd
	
	return renderEngineSelect
)

fn deleteDomes =
(
	l = for i in lights where classOf i == VrayLight and i.type == 1 collect i
				
	try(delete l)catch()
)

fn deleteSkyLights =
(
	l = for i in lights where classOf i == Skylight collect i
				
	try(delete l)catch()
)

fn checkInteger n d =
(
	if(n == undefined or n == 0 or n == "") do return d
	return n as integer
)

fn setRenderQuality =
(
	renderSceneDialog.close()
	
	r = getRender()
	q = useSettings "RENDER_QUALITY" "int" "r"
	
	renderSizeWidth = useSettings "RENDER_SIZE_X" "int" "r"
	renderSizeHeight = useSettings "RENDER_SIZE_Y" "int" "r"
	
	renderSizeWidth = checkInteger renderSizeWidth 800
	renderSizeHeight = checkInteger renderSizeHeight 800
	
	_rStep3.spnRenderSizeX.value = renderSizeWidth
	_rStep3.spnRenderSizeY.value = renderSizeHeight
	_rStep0.sldQuality.value = q
	
	_rStep3.cbxDR.visible = true
	_rStep3.btnOpenEditor.visible = true
	
	renderSystem = renderers.current 
	
	x = 800
	y = 800
	
	s = case q of
	(
		1: 
		(
			x = (renderSizeWidth / 2) as integer
			y = (renderSizeHeight / 2) as integer
		)
		2: 
		(
			x = renderSizeWidth
			y = renderSizeHeight	
		)
		default: 
		(
			x = (renderSizeWidth / 2.2) as integer
			y = (renderSizeHeight / 2.2) as integer
		)
	)
					
	renderWidth = x
	renderHeight = y
		
	case r of 
	(	
		#Scanline: 		
		(	
			_rStep3.cbxDR.visible = false
			_rStep3.btnOpenEditor.visible = false
			
			rendForce2Side = true
			scanlineRender.antiAliasFilter = Catmull_Rom()
			LT = SceneRadiosity.radiosity
			if(LT  != undefined and classOf LT == Light_Tracer) do
			(
				case q of 
				(
					2: LT.rays = 100
					3: LT.rays = 250
					default: LT.rays = 10
				)
			)
		)
		#Corona: 
		(
			renderSystem.denoise_filterType = 2
			renderSystem.denoise_blendAmount = 0.5
	
			renderSystem.colorMap_simpleExposure = 1.0
			renderSystem.colorMap_highlightCompression = 2.0	
			renderSystem.bg_overrideRefract = on
			renderSystem.bg_colorRefract = white
			renderSystem.bg_overrideDirect = on
			renderSystem.bg_colorDirect = white
			renderSystem.image_prefilter_type = 3
			renderSystem.image_prefilter_width  = 1.43
		)
		#Vray:
		(						
			renderSystem.gi_on = true
			
			renderSystem.dmc_subdivs_mult = 0.0
			renderSystem.dmc_earlyTermination_minSamples  = 32
			renderSystem.dmc_earlyTermination_amount = 0.85
			renderSystem.dmc_earlyTermination_threshold = 0.005
			renderSystem.environment_refract_on = on
			renderSystem.environment_refract_color = white
			renderSystem.system_region_x = 24
			renderSystem.system_region_y = 24
			renderSystem.colorMapping_affectBackground = on
			renderSystem.colorMapping_subpixel = on
			renderSystem.colorMapping_clampOutput = on
			renderSystem.colorMapping_clampLevel = 1.0
			renderSystem.colorMapping_type = 6
			renderSystem.colorMapping_darkMult = 1.0
			renderSystem.colorMapping_brightMult = 1.0
			renderSystem.environment_gi_on = true
			renderSystem.environment_gi_color = black
			renderSystem.filter_on  = on
			renderSystem.filter_kernel = Catmull_Rom()
					
			case q of 
			(
				1:
				(
					renderSystem.imageSampler_type = 1
					
					renderSystem.imageSampler_shadingRate = 2				
					renderSystem.twoLevel_baseSubdivs = 1				
					renderSystem.twoLevel_fineSubdivs = 4				
					renderSystem.twoLevel_threshold = 0.015									
					renderSystem.gi_primary_type = 2				
					renderSystem.gi_secondary_type = 2
					renderSystem.dmcgi_subdivs	= 4
					renderSystem.filter_on  = off
				)
				2:
				(					
					renderSystem.imageSampler_type = 1
					
					renderSystem.imageSampler_shadingRate = 15				
					renderSystem.twoLevel_baseSubdivs = 1				
					renderSystem.twoLevel_fineSubdivs = 8				
					renderSystem.twoLevel_threshold = 0.001								
					renderSystem.gi_primary_type = 2				
					renderSystem.gi_secondary_type = 2
					renderSystem.dmcgi_subdivs	= 12				
				)
				default:
				(
					renderSystem.imageSampler_type = 3
					
					renderSystem.imageSampler_shadingRate = 1				
					renderSystem.twoLevel_baseSubdivs = 1				
					renderSystem.twoLevel_fineSubdivs = 2				
					renderSystem.twoLevel_threshold = 0.015									
					renderSystem.gi_primary_type = 2				
					renderSystem.gi_secondary_type = 2
					renderSystem.dmcgi_subdivs	= 4
				)
			)
		)
	)

	try(_rStep3.checkDR type: #get)catch()
	
	renderSceneDialog.commit()
	renderSceneDialog.update()	
)

fn rebuildScript =
(	
	-- Bug when press F10
	try(fileIn (getThisScriptFileName()))catch()
)

fn onStartRender =
(
	setRenderQuality()	
)

fn onSelectionChanged =
(
	_rStep2.autoSelectLight()
)

fn isSetupScene = (fileProperties.findProperty #custom "MakePreviewSetup") == 0

fn rolloutToggle t =
(
	if(t == false) do
	(
		removeRollout _rStep1 fFastRender
		removeRollout _rStep2 fFastRender
		removeRollout _rStep3 fFastRender
		removeRollout _rPreset fFastRender
		
		return false
	)
	
	addRollout _rStep1 fFastRender rolledUp:true
	addRollout _rStep2 fFastRender rolledUp:true
	addRollout _rStep3 fFastRender
	addRollout _rPreset fFastRender rolledUp:true
	
	r = getRender()
	
	if(r == #Scanline) then
	(
		removeRollout _rStep1 fFastRender
	) else
	(
		if(not isSetupScene()) do addRollout _rStep1 fFastRender rolledUp:true
	)
)

rollout _rStep0  "Basic Setup" category:1
(
	group "Settings"
	(
		label lblAOSize "AO Size:" across: 2 align: #left
		spinner spnAOSize "" range: [0.001, 99999999999.0, (units.decodeValue "8.0cm")] type: #worldunits fieldWidth: 50 align: #center offset: [-42, 0]
		slider sldQuality "Quality:" ticks: 2 range:[0, 2, 0] type: #integer
		label lblQualityLow "Draft" align: #left across: 3
		label lblQualityMedium "Preview" align: #center
		label lblQualityHigh "Final" align: #right
	)
		
	button btnSetupScene "Setup Scene" height: 30 width: 215
		
	fn disableUI d =
	(
		for i in _rStep0.controls do i.enabled = d					
	)	
	
	on _rStep0	open do
	(						
		callbacks.removeScripts id:#renderChange
		callbacks.removeScripts id:#postNew
		callbacks.removeScripts id:#postReset
		callbacks.removeScripts id:#preRender
		callbacks.removescripts id:#selectionSetChanged
				
		--callbacks.addScript #postRendererChange "rebuildScript()" id:#renderChange
		callbacks.addScript #systemPostNew "rebuildScript()" id:#postNew
		callbacks.addScript #systemPostReset "rebuildScript()" id:#postReset
		callbacks.addScript #preRender "onStartRender()" id:#preRender
				
		callbacks.addScript #selectionSetChanged "onSelectionChanged()" id:#selectionSetChanged persistent:false			
	)
	
	on _rStep0 close do
	(
		callbacks.removeScripts id:#renderChange
		callbacks.removeScripts id:#postNew
		callbacks.removeScripts id:#postReset		
		callbacks.removeScripts id:#preRender		
		callbacks.removescripts id:#selectionSetChanged
	)
		
	on btnSetupScene pressed do
	(		
		q = setupRenderEngine()
		if(q == #none) do return false
		
		deleteDomes()
		deleteSkyLights()
		
		r = getRender()
						
		if(r == #unknown) do return messageBox "Please assign Corona or V-Ray Renderer!" title: "Warning!" 
			
		_rStep1.setHDRI()
		
		--_rStep1.firstLoad()
					
		fileProperties.addProperty #custom "MakePreviewSetup" true
			
		rolloutToggle true
		
		setRenderQuality()
	)
			
	on spnAOSize changed v do
	(
		useSettings "AO_SIZE" v "w"
	)
	
	on sldQuality changed v do
	(	
		useSettings "RENDER_QUALITY" v "w"
				
		setRenderQuality()
	)
)

rollout _rStep1  "HDRI" category:2
(
	group "Light"
	(		
		spinner spnLightHDR "Angle:" across: 2 align: #left range: [0, 360, 0] type:#integer scale: 1.0 
		spinner spnLightHDR_Mult "Mult.:" align: #right range: [0.0, 9999.0, 1.0]  type:#float scale: 0.1  
		label lblStart "0 deg." align: #left across: 2
		label lblStop "360 deg." align: #right
		slider sldLightHDR "" ticks: 36 range:[0,360,0] type: #integer
		button btnLightHDR "None" across: 2 width: 100 offset: [45, 0]
		button btnResetLightHDR "R" tooltip: "Reset to default" offset: [2, 0]
	)
	
	group "Reflections"
	(
		checkbox cbxUseReflect "Use Reflect HDR"
		spinner spnReflectHDR "Angle:" across: 2 align: #left range: [0, 360, 0] type: #integer scale: 1.0 		
		spinner spnReflectHDR_Mult "Mult.:"align: #right range: [0.0, 9999.0, 1.0]  type:#float scale: 0.1  
		label lblStart2 "0 deg." align: #left across: 2
		label lblStop2 "360 deg." align: #right
		slider sldReflectHDR "" ticks: 36 range:[0,360,0] type: #integer
		button btnReflectHDR "None" across: 2 width: 100 offset: [45, 0]
		button btnResetReflectHDR "R" tooltip: "Reset to default" offset: [2, 0]
	)
	
	timer tmrRotateLightHDR "" active: false interval: 100
	timer tmrRotateReflectHDR "" active: false interval: 100
	
		
	-------------------------------------------------
	
	
	fn delVFB_Elements =
	(
		re = maxOps.GetCurRenderElementMgr()
		re.RemoveAllRenderElements()
	)

	fn addElement el =
	(		
		re = maxOps.GetCurRenderElementMgr()				
		re.AddRenderElement el	
	)

	fn addVFB_Elements = 
	(
		delVFB_Elements()

		renderElements = #()
		
		r = getRender()
		case r of 
		(
			#Corona:
			(
				append renderElements (CESSENTIAL_Direct())
				append renderElements (CESSENTIAL_Reflect())
				append renderElements (CESSENTIAL_Refract())
				append renderElements (CShading_Albedo())
		
				cm = CTexmap()	
				cm.texmap = CoronaAO()	
				cm.overrideBackground = true
				cm.overrideBackgroundColor = white
				cm.texmap.maxDistance = _rStep0.spnAOSize.value
				
				append renderElements cm
					
				cm2 = CTexmap()	
				cm2.texmap = CoronaWire()	
				cm2.texmap.baseColor = black
				cm2.texmap.edgeColor = white
				cm2.texmap.edgeWidthPixels = 0.5
				
				append renderElements cm2
			)
			#VRay:
			(
				append renderElements (VRayGlobalIllumination())
				append renderElements (VRayReflection())
				append renderElements (VRayRefraction())
				
				-- AO	
				cm = VrayExtraTex() 				
				cm.texture = VRayDirt()
				cm.texture.radius = _rStep0.spnAOSize.value
				
				append renderElements cm
					
				-- Wire	
				cm2 = VrayExtraTex() 				
				cm2.texture = VrayEdgesTex()
				cm2.texture.pixelWidth = 0.5
				
				append renderElements cm2
			)
		)
		
		
		for el in renderElements do addElement el
	)
		
	-------------------------------------------------
	
	fn disableUI d =
	(
		for i in _rStep1.controls do i.enabled = d					
	)
		
	fn defaultView = 
	(
		select geometry
				
		viewport.setType #view_front
		viewport.setType #view_persp_user 		
					
		theAxis = (viewport.getTM()).row1	
		viewport.rotate (quat 20 theAxis)
	
		theAxis = (viewport.getTM()).row3
		viewport.rotate (quat 40 theAxis)
		
		max zoomext sel
		viewport.zoom 0.8
		
		displaySafeFrames = true	
			
		--c = freeCamera transform:(inverse(viewport.getTM())) fov: 55	
		--viewport.setCamera c
	)
	
	fn getDome =
	(
		domes = for l in lights where classOf l == VRayLight and l.type == 1 collect l
		if(domes.count != 1) do return undefined
		return domes[1]
	)
	
	fn getSkylight =
	(
		domes = for l in lights where classOf l == Skylight collect l
		if(domes.count != 1) do return undefined
		return domes[1]
	)
	
	fn angeToOffset a = 1.0 / (360.0 / a) 
	fn offsetToAngle o = 360.0 * o
	fn getAngle a = ((a as integer) as string) + "°"
	
	fn setOffset b o =
	(
		case classOf b of
		(
			CoronaBitmap: b.uvwOffset = [o, 0, 0]
			bitmapTex: b.coords.U_Offset = o
		)
	)
	
	fn getOffset h =
	(
		case classOf h of
		(
			CoronaBitmap: return h.uvwOffset[1]
			bitmapTex: return h.coords.U_Offset
			default: return 0.0
		)
	)		
	
	fn getHDR h =
	(
		case classOf h of
		(
			CoronaBitmap: return h
			bitmapTex: return h
			colorCorrection: return h.map
			default: return undefined
		)	
	)
	
	fn setMult h i =
	(			
		if(i == "") do i = 1.0
		
		i = i as float
		
		h = getHDR h
		if(h == undefined) do return false
		
		case classOf h of
		(
			CoronaBitmap:  h.output.output_amount = i
			bitmapTex:  h.output.output_amount = i
		)
	)
	
	fn getMult h =
	(
		h = getHDR h
		
		case classOf h of
		(
			CoronaBitmap:  return h.output.output_amount
			bitmapTex:  return h.output.output_amount
			default: return 1.0
		)	
	)
	
	fn rotateHDR b a = 
	(
		if(b == undefined) do return false
		o = angeToOffset a
			
		h = getHDR b
		if(h != undefined) do setOffset h o				
	)
		
	fn loadHDR = 
	(
		r = getRender()
		
		case r of 
		(
			#Corona: 
			(
				if(renderSystem.bg_source != 1) do return false
			
				h = getHDR renderSystem.bg_texmap
				if(h != undefined) do 
				(
					btnLightHDR.caption = filenameFromPath h.filename
										
					o = getOffset h
					spnLightHDR.value = offsetToAngle o
					sldLightHDR.value = offsetToAngle o
					
					spnLightHDR_Mult.value = getMult h
				)
			
				h = getHDR	 renderSystem.bg_texmapReflect
				if(h != undefined) do 
				(
					btnReflectHDR.caption = filenameFromPath h.filename	
					
					o = getOffset h
					spnReflectHDR.value = offsetToAngle o 
					sldReflectHDR.value = offsetToAngle o
					
					spnReflectHDR_Mult.value = getMult h
				)
				
				cbxUseReflect.checked = renderSystem.bg_overrideReflect
			)
			#VRay:
			(							
				dome = getDome()
				
				if(dome == undefined) do return false
				
				h = getHDR dome.texmap
				if(h != undefined) do
				(
					btnLightHDR.caption = filenameFromPath h.filename
										
					o = getOffset h
					spnLightHDR.value = offsetToAngle o
					sldLightHDR.value = offsetToAngle o
					
					spnLightHDR_Mult.value = getMult h
				)

				h = getHDR renderSystem.environment_rr_map
				
				if(h != undefined) do 
				(
					btnReflectHDR.caption = filenameFromPath h.filename	
					
					o = getOffset h
					spnReflectHDR.value = offsetToAngle o 
					sldReflectHDR.value = offsetToAngle o
					
					spnReflectHDR_Mult.value = getMult h
				)
				
				cbxUseReflect.checked = renderSystem.environment_rr_on								
				dome.affect_reflections = not cbxUseReflect.checked
			)
		)			
	)
	
	fn setHDRI =
	(
		f = getFilenamePath  (getThisScriptFilename ()) + @"hdri\" 
		lightHDRPath = f + "light.hdr" 
		reflectHDRPath = f + "reflect.hdr" 
		
		r = getRender()
		
		f1 = useSettings "LIGHT_HDR" "str" "r"	
		f2 = useSettings "REFLECT_HDR" "str" "r"	
			
		lightHDRPath = if(f1 != "")	then f1 else lightHDRPath
		reflectHDRPath = if(f2 != "") then f2 else reflectHDRPath	
			
		case r of 
		(
			#Corona: 
			(
				renderSystem.bg_source = 1
				
				if(doesFileExist lightHDRPath) do
				(
					renderSystem.bg_overrideDirect = true
					renderSystem.bg_colorDirect = white
					
					lightHDR = CoronaBitmap()
					lightHDR.filename = lightHDRPath
					renderSystem.bg_texmap = lightHDR
						
					a = useSettings "LIGHT_HDR_ANGLE" "float" "r"						
					rotateHDR (renderSystem.bg_texmap) a

					a = useSettings "LIGHT_HDR_MULT" "string" "r"	
					setMult renderSystem.bg_texmap a
				)
				
				if(doesFileExist reflectHDRPath) do
				(
					renderSystem.bg_overrideReflect = true			
					reflectHDR = CoronaBitmap()
					reflectHDR.filename = reflectHDRPath
					renderSystem.bg_texmapReflect = reflectHDR
					
					a = useSettings "REFLECT_HDR_ANGLE" "float" "r"						
					rotateHDR (renderSystem.bg_texmapReflect) a	

					a = useSettings "REFLECT_HDR_MULT" "string" "r"	
					setMult renderSystem.bg_texmapReflect a			
				)
			)	
			#Scanline:
			(
				backgroundColor = white	
				dome = getSkylight()
				if(dome == undefined) do dome = Skylight()
				dome.sky_mode = 0
				dome.multiplier = 0.9
								
				LT = Light_Tracer()
				RadiosityPreferences.useAdvancedLighting = true
				RadiosityPreferences.computeAdvancedLighting = true

				SceneRadiosity.radiosity = LT
			)
			#VRay:
			(								
				if(doesFileExist lightHDRPath) do
				(
					backgroundColor = white	

					lightHDR = BitmapTex()
					lightHDR.filename = lightHDRPath
					lightHDR.coords.mappingtype = 1
					
					dome = getDome()
					if(dome == undefined) do dome = VrayLight()
										
					dome.type = 1
					dome.multiplier = 1.0
					dome.dome_spherical = true
					dome.invisible = on
					dome.texmap = lightHDR
					
					a = useSettings "LIGHT_HDR_ANGLE" "float" "r"						
					rotateHDR (dome.texmap) a

					a = useSettings "LIGHT_HDR_MULT" "string" "r"	
					setMult dome.texmap a	

					a = useSettings "REFLECT_HDR_ON" "bool_not" "r"						
					dome.affect_reflections = not a
				)
				
				if(doesFileExist reflectHDRPath) do
				(
					renderSystem.environment_rr_on = true			
					renderSystem.environment_rr_map_on = true			
									
					
					reflectHDR = BitmapTex()
					reflectHDR.filename = reflectHDRPath
					reflectHDR.coords.mappingtype = 1
					renderSystem.environment_rr_map = reflectHDR
					
					a = useSettings "REFLECT_HDR_ANGLE" "float" "r"						
					rotateHDR (renderSystem.environment_rr_map) a	

					a = useSettings "REFLECT_HDR_MULT" "string" "r"	
					setMult renderSystem.environment_rr_map a			
				)
			)
		)
				
		loadHDR()
	)
	
	fn useReflect =
	(
		c = useSettings "REFLECT_HDR_ON" "bool_not" "r"	
		cbxUseReflect.checked = c
		
		r = getRender()
		
		case r of 
		(
			#Corona: 
			(
				renderSystem.bg_overrideReflect = c
			)
			#VRay:
			(
				renderSystem.environment_rr_on = c			
				renderSystem.environment_rr_map_on = c	
			)
		)
		
		spnReflectHDR.enabled = c
		spnReflectHDR_Mult.enabled = c
		lblStart2.enabled = c
		lblStop2.enabled = c
		sldReflectHDR.enabled = c
		btnReflectHDR.enabled = c
		btnResetReflectHDR.enabled = c
	)

	fn getUnit =
	(
		u = units.SystemType	
		case u of
		(
			#Inches: return "in"
			#Feet: return "feet"
			#Miles: return "ml"
			#Millimeters: return "mm"
			#Centimeters: return "cm"
			#Meters: return "m"
			#Kilometers: return "km"
			default: "generic"
		)
	)
	
	fn setupScene = 
	(
		if(renderSystem == undefined) do return messageBox "Please assign correct render!" title: "Warning!"
				
		defaultView()
				
		setHDRI()
		
		useReflect()
				
		addVFB_Elements()
				
		setRenderQuality()			
	)
	
	fn firstLoad = 
	(
		r = getRender()
		
		if(r == #unknown) do 
		(
			closeRolloutFloater fFastRender
			return messageBox "Please assign Corona or V-Ray Renderer!" title: "Warning!"
		)
								
		renderSystem = renderers.current 
				
		loadHDR()
		useReflect()
						
		s = useSettings "AO_SIZE" "int" "r"
		if(s != 0) do _rStep0.spnAOSize.value = s
	)
	
	on btnLightHDR pressed do
	(
		f = getOpenFileName  caption: "Select Light HDR" types: "*.hdr|*.hdr|*.exr|*.exr"
		if(f == undefined) do return false
		
		useSettings "LIGHT_HDR" f "w"	

		setHDRI()
	)
	
	on btnReflectHDR pressed do
	(
		f = getOpenFileName  caption: "Select Reflect HDR" types: "*.hdr|*.hdr|*.exr|*.exr"
		if(f == undefined) do return false
		
		useSettings "REFLECT_HDR" f "w"	

		setHDRI()
	)
	
	on cbxUseReflect changed s do
	(
		useSettings "REFLECT_HDR_ON" s "w"
		
		dome = getDome()
		if(dome != undefined) do dome.affect_reflections = not s
		
		useReflect()
	)
	
	on _rStep1	open do
	(			
		firstLoad()
	)
		
	on sldLightHDR changed a do
	(							
		spnLightHDR.value = a 				
		
		tmrRotateLightHDR.active = false
		tmrRotateLightHDR.active = true
	)
	
	on tmrRotateLightHDR tick do
	(
		r = getRender()
		a = spnLightHDR.value 
		
		case r of
		(
			#Corona: rotateHDR (renderSystem.bg_texmap) a			
			#VRay: 
			(
				dome = getDome()
				if(dome != undefined) do rotateHDR (dome.texmap) a									
			)
		)
		
		
		useSettings "LIGHT_HDR_ANGLE" a "w"
		
		tmrRotateLightHDR.active = false
	)
	
	on tmrRotateReflectHDR tick do
	(
		r = getRender()
		a = spnReflectHDR.value
		
		case r of
		(
			#Corona: rotateHDR (renderSystem.bg_texmapReflect) a			
			#VRay: rotateHDR (renderSystem.environment_rr_map) a	
		)
				
		useSettings "REFLECT_HDR_ANGLE" a "w"
		
		tmrRotateReflectHDR.active = false
	)
	
	on sldReflectHDR changed a do
	(					
		spnReflectHDR.value = a 
				
		tmrRotateReflectHDR.active = false
		tmrRotateReflectHDR.active = true
	)
	
	on sldReflectHDR buttondown do
	(
		tmrRotateReflectHDR.active = false
		tmrRotateReflectHDR.active = true
	)
	
	on sldLightHDR buttondown do
	(
		tmrRotateLightHDR.active = false
		tmrRotateLightHDR.active = true
	)
	
	on spnReflectHDR changed a do
	(
		sldReflectHDR.value = a
		
		tmrRotateReflectHDR.active = false
		tmrRotateReflectHDR.active = true
	)
	
	on spnLightHDR changed a do
	(
		sldLightHDR.value = a
		
		tmrRotateLightHDR.active = false
		tmrRotateLightHDR.active = true
	)
	
	on spnLightHDR_Mult changed v do
	(
		r = getRender()
		
		case r of
		(
			#Vray:
			(
				dome = getDome()
				if(dome != undefined) do setMult dome.texmap v
			)
			#Corona: setMult renderSystem.bg_texmap v
		)
				
		useSettings "LIGHT_HDR_MULT" v "w"
	)
	
	on spnReflectHDR_Mult changed v do
	(				
		r = getRender()
		
		case r of
		(
			#Vray:
			(
				setMult renderSystem.environment_rr_map v
			)
			#Corona: setMult renderSystem.bg_texmapReflect v
		)
		
		useSettings "REFLECT_HDR_MULT" v "w"
	)
	
	on btnResetLightHDR pressed do
	(		
		q = queryBox "Do you really want to reset to default HDR?" title: "Reset to default!"
		if(not q) do return false
		
		useSettings "LIGHT_HDR" "" "w"
		setHDRI()
	)
	
	on btnResetReflectHDR pressed do
	(		
		q = queryBox "Do you really want to reset to default HDR?" title: "Reset to default!"
		if(not q) do return false
		
		useSettings "REFLECT_HDR" "" "w"
		setHDRI()
	)
)

rollout _rStep2  "Highlights" category:3
(	
	listbox lbxLights height: 8
	button btnAdd "Add Light" across: 2
	button btnRemove "Remove Light"
	
	group "Light Settings"
	(
		spinner spnW "W:" range: [0, 99999999, (units.decodeValue "80.0cm")] fieldWidth: 60 type: #worldunits across: 2 align: #left offset: [14, 0]
		spinner spnH "H:" range: [0, 99999999, (units.decodeValue "50.0cm")] fieldWidth: 60 type: #worldunits  align: #right
		spinner spnMult "Mult.:" range: [0, 999999, 1.0] fieldWidth: 60 type: #float align: #left offset: [0, 10] across: 2 scale: 0.5
		spinner spnDist "Dist.:" range: [0, 99999999, (units.decodeValue "150.0cm")] fieldWidth: 60 type: #worldunits  align: #right offset: [0, 10] scale: 1.0
		spinner spnScale "Scale.:" range: [0, 99999999, 1.0] fieldWidth: 60 type: #float  scale: 0.05 offset: [0, 10] align: #right 
	)
	
	button btnPlace "Place Highlight" width: 215 height: 30
	
	timer tmrInteractive active: false 
		
	local callbackLight = undefined
	global lightsList = #()
	local trackCallback = #continue
	
	fn getCurrentLight =
	(
		i = lbxLights.selection
		if(i == 0) do return undefined
		
		l = lightsList[i]
		if(isValidNode l) do return l
		--for l in lightsList where isValidNode l and findString l.name n != undefined do return l
				
		return undefined
	)
	
	fn disableUI d =
	(
		for i in _rStep2.controls do i.enabled = d
		btnPlace.enabled = true
	)

	
	fn trackRay ir placeObj =	
	(		
		if(ir != undefined) then
		(	
			view = (inverse(viewport.getTM())).row4
			viewVector = normalize (view - ir.pos)
					
			refVector = normalize (2 * (dot ir.dir viewVector) *  ir.dir - viewVector)
		
			placeObj.dir = refVector
				
			placeObj.pos = ir.pos 
			in coordsys local move placeObj [0, 0, spnDist.value]
			
			setUserProp placeObj "HiglightDistance" (distance placeObj.pos ir.pos)	
			
			return #continue
		)
	)
	
	fn placeObject objList l =
	(		
		global placeObj = l
				
		fn mouseTrackCallback msg ir obj faceNum shift ctrl alt =
		(
			case msg of
			(				
				#freeMove:
				(						
					return #continue
				)
				#mouseAbort:
				(
					return #end
				)
				#mousePoint:
				(	
					trackRay ir placeObj		
					--tmrInteractive.active = true						
					return #continue
				)
				#mouseMove:
				(																								
					trackRay ir placeObj
					return #continue
				)									
			)
		)
				
		trackCallback = mouseTrack on:objList trackCallBack: mouseTrackCallback		
	)
	
	fn getLights = return for l in lights where findString l.name "HighLight_" != undefined collect l

	fn getParams = 
	(
		l = getCurrentLight()
		r = getRender()
		
		if(l == undefined) do return false
				
		case r of
		(
			#Corona:
			(
				try(spnW.value = l.width)catch()
				try(spnH.value = l.height)catch()
				try(spnMult.value = l.intensity)catch()
			)
			#VRay:
			(
				try(spnW.value = l.size0)catch()
				try(spnH.value = l.size1)catch()
				try(spnMult.value = l.multiplier)catch()
			)
		)
				
		d = getUserProp l "HiglightDistance" 
		if(d != undefined) do spnDist.value = d
		
		spnScale.value = l.scale.z
	)
		
	fn setParams =
	(
		l = getCurrentLight()		
		if(l == undefined) do return false
		
		r = getRender()
		
		case r of
		(
			#Corona:
			(
				l.width = spnW.value
				l.height = spnH.value
				l.intensity = spnMult.value 
			)
			#VRay:
			(
				l.size0 = spnW.value
				l.size1 = spnH.value
				l.multiplier = spnMult.value 
			)
		)
				
		d = getUserProp l "HiglightDistance" 
				
		v = spnDist.value - d 
			
		m = d  + v
		
		in coordsys local move l [0, 0, v]
		setUserProp l "HiglightDistance" m

		s = spnScale.value
		l.scale = [s, s, s]
	)
	
	fn autoSelectLight =
	(
		s = selection
		if(s.count == 0 or superClassOf s[1] != light) do return false
		
		l = lbxLights.items
		i = findItem l s[1].name
		if(i != 0) do 
		(
			getParams()	
			lbxLights.selection = i
		)
	)
	
	fn buildList ev nd =
	(
		lightsList = getLights()
		lbxLights.items = for i in lightsList collect i.name
			
		getParams()
	)
	
	fn addLight =
	(
		f = getFilenamePath  (getThisScriptFilename ()) + @"hdri\highlight.exr" 

		h = (units.decodeValue "80.0cm")
		w = (units.decodeValue "50.0cm")
		l = undefined

		r = getRender()
			
		case r of
		(			
			#Corona: l = CoronaLight targeted:off height: h width: w pos:[0, 0, 0] isSelected: off name: (uniqueName "HighLight_") visibleDirectly: off shape: 1 intensity: 25
			#VRay: l = VRayLight targeted:off size0: h size1: w pos:[0, 0, 0] isSelected: off name: (uniqueName "HighLight_") invisible: on type: 0 multiplier: 25
			#Scanline: l = OmniLight tpos:[0, 0, 0] isSelected: off name: (uniqueName "HighLight_") multiplier: 1
		)
		
		setUserProp l "HiglightDistance" (units.decodeValue "150.0cm")
			
		s = lbxLights.items.count
		if(s > 0) do try(lbxLights.selection = s + 1) catch()
			
		if(doesFileExist f) do
		(
			case r of
			(			
				#Corona: 
				(
					l.colorMode = 2
					l.texmap = CoronaBitmap filename: f
				)
				#VRay:
				(
					l.texmap_on = true
					l.texmap = BitmapTex filename: f
				)
			)
		)
	)
		
	fn removeLight =
	(				
		l = getCurrentLight()
		
		if(l != undefined) do delete l
	)
	
	on tmrInteractive tick do 
	(			
		CoronaRenderer.CoronaFp.startInteractive()
		
		tmrInteractive.active = false
	)
	
	on spnW changed v do setParams()
	on spnH changed v do setParams()
	on spnMult changed v do setParams()
	on spnDist changed v do setParams()
	on spnScale changed v do setParams()
		
	on btnAdd pressed do addLight()
	on btnRemove pressed do removeLight()
	
	on _rStep2 open do
	(
		r = getRender()
		if(r == #Scanline) do
		(
			_rStep2.spnW.visible = false
			_rStep2.spnH.visible = false
			_rStep2.spnScale.visible = false
		)
		
		buildList undefined undefined
		
		callbackLight = NodeEventCallback mouseUp:true delay:1000 deleted:buildList added:buildList nameChanged:buildList
	)
	
	on _rStep2 close do
	(
		callbackLight = undefined
	)
	
	on lbxLights selected a do getParams()	

	
	on btnPlace pressed do
	(			
		placeObj = getCurrentLight()
		
		if(placeObj == undefined) do return messageBox "Please add highlight!" title: "Warning!"
		
		disableUI false
				
		o = for i in geometry where i.isHidden == false and placeObj != i  collect i
		--while trackCallback != #stop do placeObject o placeObj
		placeObject o placeObj
				
		trackCallback = #continue
				
		disableUI true
	)
	
	on lbxLights doubleClicked v do
	(
		n = lbxLights.selected 
		l = getNodeByName n exact: true ignoreCase: false
		try(if(superClassOf l == light) do select l)catch()
	)
)

fn postProcess =
(
	global rPostProcess
	global rPostSettings
	
	global postRenderProcessImage = undefined
	global btmPreview = undefined
	
	try(destroyDialog rPostProcess) catch()
	
	global postRender = undefined
	
	rollout rPostProcess "Post Process"
	(
		dotNetControl btmPreview "System.Windows.Forms.PictureBox" width: 800 height: 800 pos: [0, 0]	
	
		subrollout subRollout1 "Settings"
		
		timer tmrShowRender "" active: false
				
		fn preRenderImageFromMap = renderMap outputMap size:[800, 800] filter: false scale: 1.0 gamma: 2.2
		
		fn setBitmap b =
		(
			if(b == undefined) do return false
			
			bs = dotNetclass "System.Windows.Forms.PictureBoxSizeMode"
			btmPreview.SizeMode = bs.Zoom 
			
			if(btmPreview.width > b.width and btmPreview.height > b.height) do btmPreview.SizeMode = bs.CenterImage
				
			setClipboardBitmap  b	
			c = dotNetClass "System.Windows.Forms.Clipboard"
			img = c.GetImage()	
			r = dotNetObject "System.Drawing.Rectangle" 0 0 img.width img.height			
			gu = (dotNetClass "System.Drawing.GraphicsUnit").Pixel			
			a = dotNetObject "System.Drawing.Imaging.ImageAttributes"
			a.SetGamma 0.45			
			g = (dotNetClass "System.Drawing.Graphics").FromImage(img)	
			g.DrawImage img r 0 0 img.width img.height gu a			
			c.setImage(img)			
			btmPreview.image = c.GetImage()
		)
		
		fn clearRender = 
		(
			btmPreview.image = undefined
		)
		
		fn showRender now: false = 
		(	
			if(now != true ) do clearRender()
			
			postRenderProcessImage = preRenderImageFromMap()
			
			tmrShowRender.interval = 100
			if(now == true ) do tmrShowRender.interval = 1
			
			tmrShowRender.active = true
		)
				
		fn saveImage =
		(
			f = getSaveFileName types: "JPG|*.jpg|PNG|*.png"
			if(f == undefined) do return false
			
			r = preRenderImageFromMap()
			
			r.filename = f
			save r gamma: 2.2
			close r
		)
		
		fn showWire show: undefined inv: undefined op: undefined blnd: undefined =
		(			
			i = outputMap.mapList.count			
			
			if(show != undefined) do outputMap.mapEnabled[i] = if(show == 2) then true else false
			if(op != undefined) do outputMap.opacity[i] =  op		
			if(outputMap.mapList[i] != undefined and inv != undefined) do outputMap.mapList[i].output.invert =  inv
			if(blnd == false) then
			(
				outputMap.blendMode[i] = 0
			)
			else
			(
				outputMap.blendMode[i] = if(inv == false) then 9 else 5
			)
			
			rPostProcess.showRender now: true
		)
		
		on rPostProcess open do
		(				
			showRender()			
		)
		
		on tmrShowRender tick do
		(
			setBitmap postRenderProcessImage
			
			tmrShowRender.active = false
		)
		
		on rPostProcess close do
		(
			try(destroyDialog rPostProcess) catch()
		)
	)
	
	createDialog rPostProcess (800 + 250) 800

	
	ex = "rollout rPostSettings \"Settings\"\n "
	ex += "(\n"
	ex += "checkbox cbxShowAll \"Hide/Show Effects\" checked: true\n\n"
	
		
	for i in 1 to outputMap.mapList.count - 1 where outputMap.mapList[i] != undefined do 
	(	
		n = outputMap.mapList[i].name
		b = outputMap.blendMode[i]
		o = outputMap.opacity[i] as string 
		s = i as string
		en = if(i == 1) then "false" else "true"
		-- Save Settings
		/*
			blendMode = useSettings ("BLENDMODE" + s) "string" "r"
			if(blendMode != "") do b = blendMode as integer - 1
			
			opacityMode = useSettings ("OPACITY" + s) "string" "r"
			if(opacityMode != "") do o = opacityMode
			
			enableMode = useSettings ("EFFECT" + s) "string" "r"
			if(enableMode == "") do enableMode = "true"
			
			outputMap.blendMode[i] = b
			outputMap.opacity[i] = o as integer
		*/
		
		enableMode = "true"
		
		ex += "group \"" + n + "\" \n"
		ex += "(\n"
		ex += "checkbox cbx" + s + " \"\" width: 24 across: 3 checked: " + enableMode + "  enabled: " + en + " \n"
		ex += 	"dropdownlist ddl" + s+ "\"\" width: 140 enabled: " + en + " offset: [-45, 0] items: blendOptions selection: " + (b + 1) as string + "\n"
		ex += "spinner spn" + s + "\"\" range: [0, 100, " + o + "] type: #integer enabled: " + en + " \n"
		ex += ")\n"
		ex += "on ddl" + s+ " selected v do (\n"
		ex += 	"outputMap.blendMode[" + s +"] = v - 1\n"
		ex += 	"rPostProcess.showRender now: true\n"
		ex += 	"useSettings \"BLENDMODE" + s + "\" v \"w\" \n"
		ex += ")\n"	
		ex += "on spn" + s + " changed v do (\n"
		ex += 	"outputMap.opacity[" + s + "] = v \n"
		ex += 	"rPostProcess.showRender now: true\n"
		ex += 	"useSettings \"OPACITY" + s + "\" v \"w\" \n"		
		ex += ")\n"
		ex += "on cbx" + s + " changed  v do (\n"
		ex += 	"if(v == false) then outputMap.opacity[" + s + "] = 0 else outputMap.opacity[" + s + "] = spn" + s + ".value \n"
		ex += 	"rPostProcess.showRender now: true\n"
		ex += 	"useSettings \"EFFECT" + s + "\" v \"w\" \n"
		ex += ") \n"
	)
	
	ex += "on cbxShowAll changed s do \n"
	ex += "(\n"
	
		for i in 1 to outputMap.mapList.count - 1  where outputMap.mapList[i] != undefined  do 
		(	
			n = outputMap.mapList[i].name
			b = outputMap.blendMode[i]
			o = outputMap.opacity[i] as string 
			s = i as string
			
			if(i != 1) do 
			(
				ex += "if(s == false) then outputMap.opacity[" + s + "] = 0 else outputMap.opacity[" + s + "] = spn" + s + ".value \n"
				ex += "cbx" + s + ".checked = s \n"			
			)
		)

	ex += "rPostProcess.showRender now: true\n"
	ex += ")\n"
	ex += "group\"Wire Parameters\"(\n"
	ex += "checkbox cbxBlendWire \"Blend\" checked: false\n"
	ex += "checkbox cbxInvertWire \"Invert\" checked: true\n"	
	ex += "spinner spnOpacityWire \"Opacity: \" range: [0, 100, 100] type: #integer align: #left fieldWidth: 30\n"
	ex += ")\n"
	ex += "radiobuttons rdoWire \"\" labels:#(\"Render\", \"Wire\") offset: [0, 15]\n"
	ex += " on cbxInvertWire changed z do rPostProcess.showWire show: (rdoWire.state) inv: (cbxInvertWire.checked) op: (spnOpacityWire.value) blnd: (cbxBlendWire.checked)\n"
	ex += " on spnOpacityWire changed z do rPostProcess.showWire show: (rdoWire.state) inv: (cbxInvertWire.checked) op: (spnOpacityWire.value) blnd: (cbxBlendWire.checked)\n"
	ex += " on rdoWire changed z do rPostProcess.showWire show: (rdoWire.state) inv: (cbxInvertWire.checked) op: (spnOpacityWire.value) blnd: (cbxBlendWire.checked)\n"
	ex += " on cbxBlendWire changed z do rPostProcess.showWire show: (rdoWire.state) inv: (cbxInvertWire.checked) op: (spnOpacityWire.value) blnd: (cbxBlendWire.checked)\n"
	ex += "button btnSave \"Save Image\" offset: [0, 15]\n"
	ex += " on btnSave pressed do rPostProcess.saveImage()"
	ex += ")\n"
	
	execute(ex)
	
	rPostProcess.subRollout1.pos = [800, 0]
	rPostProcess.subRollout1.width = 250
	rPostProcess.subRollout1.height = rPostProcess.height
	
	AddSubRollout rPostProcess.subRollout1 rPostSettings		
)

rollout _rStep3  "Render / Post Process" category:4
(		
	group "Render"
	(
		--button btnStartInteractive "Start Interactive"
		label lblRenderSize "Final Render Size (px):" align: #left
		spinner spnRenderSizeX "" range: [100, 999999999, 800] fieldWidth: 50 type: #integer align: #right across: 3 offset: [20, 0]
		label lblX " x " 
		spinner spnRenderSizeY "" range: [100, 999999999, 800] fieldWidth: 50 type: #integer align: #left offset: [-20, 0]
		button btnStartRender "Start Render" height: 30 width: 215		
		checkbox cbxDR "Distributed rendering"
	)
	group "Post Process"
	(
		button btnOpenEditor "Open Editor" height: 30 width: 215
	)
	
	on spnRenderSizeX changed v do useSettings "RENDER_SIZE_X" v "w"
	on spnRenderSizeY changed v do useSettings "RENDER_SIZE_Y" v "w"
	
	timer tmrStartRender active: false interval: 200
	
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
				
		nb = bitmap 800 800 color: white 
		copy b nb
		
		t = timeStamp()
		nb.filename = f + t as string  + ".jpg"
		
		save nb gamma: 2.2
		close nb
		
		bt = Bitmaptexture()	
		bt.bitmap = nb
			
		return  bt
	)
	
	fn clearCache =
	(
		f = @"c:\temp\make-preview\"
		d = getFiles (f + "*")
		for i in d do deleteFile i
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

		-- Refract / Screen
		l = 4		
		outputMap.mapList[l] = getVFB c: l
		outputMap.mapList[l].coords.blur = 0.01
		outputMap.blendMode[l] = 9
		outputMap.opacity[l] = 50
		outputMap.mask[l] = vfbAlpha
		outputMap.mapList[l].name = "Refract"
		
		-- Albedo / Overlay
		l = 5		
		outputMap.mapList[l] = getVFB c: l
		outputMap.mapList[l].coords.blur = 0.01
		outputMap.blendMode[l] = 14
		outputMap.opacity[l] = 25
		outputMap.mask[l] = vfbAlpha
		outputMap.mapList[l].name = "Albedo"

		-- AO / Multiply
		l = 6		
		outputMap.mapList[l] = getVFB c: l
		outputMap.mapList[l].coords.blur = 0.01
		outputMap.blendMode[l] = 5
		outputMap.opacity[l] = 25
		outputMap.mask[l] = vfbAlpha
		outputMap.mapList[l].name = "AO"
		
		-- Wire / Screen
		l = 7
		outputMap.mapList[l] = getVFB c: l
		outputMap.mapList[l].coords.blur = 0.01
		outputMap.blendMode[l] =9
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

		-- Refract / Screen
		l = 4		
		outputMap.mapList[l] = getVFB c: 5
		outputMap.mapList[l].coords.blur = 0.01
		outputMap.blendMode[l] = 9
		outputMap.opacity[l] = 50
		outputMap.mask[l] = vfbAlpha
		outputMap.mapList[l].name = "Refract"


		-- AO / Multiply
		l = 5		
		outputMap.mapList[l] = getVFB c: 6
		outputMap.mapList[l].coords.blur = 0.01
		outputMap.blendMode[l] = 5
		outputMap.opacity[l] = 25
		outputMap.mask[l] = vfbAlpha
		outputMap.mapList[l].name = "AO"
		
		-- Wire / Screen
		l = 6	
		outputMap.mapList[l] = getVFB c: 7
		outputMap.mapList[l].coords.blur = 0.01
		outputMap.blendMode[l] = 9
		outputMap.opacity[l] = 0
		outputMap.mask[l] = vfbAlpha
		outputMap.mapList[l].name = "Wire"
	)
	
	fn setupPostProcess =
	(
		clearCache()
		
		_rStep1.addVFB_Elements()
		
		
		r = getRender()
		
		case r of
		(
			#Corona: postProcessCorona()
			#VRay: postProcessVRay()
		)				
	)
	
	fn checkDR type: #get =
	(		
		r = getRender()
		
		if(type == #set) do
		(
			case r of
			(
				#Corona: renderSystem.dr_enable = cbxDR.checked
				#VRay: renderSystem.system_distributedRender = cbxDR.checked
			)		
		)	
		
		if(type == #get) do		
		(
			case r of
			(
				#Corona: cbxDR.checked = renderSystem.dr_enable
				#VRay: cbxDR.checked = renderSystem.system_distributedRender
			)	
		)
	)

	global isRendering = true
	
	
	fn stopRender = CoronaRenderer.CoronaFp.stopRender()
	
	fn startInteractive =
	(		
		CoronaRenderer.CoronaFp.startInteractive()
	)
	
	on tmrStartRender tick do
	(
		tmrStartRender.active = false
		max quick render
	)
	
	on btnStartRender pressed do 
	(	
		r = getRender()
		
		setRenderQuality()
				
		if(_rStep0.sldQuality.value == 0 and r == #Corona) do
		(
			startInteractive()
			return false
		)
		
		tmrStartRender.active = true		
	)
		
	on btnOpenEditor pressed do 
	(			
		try(setupPostProcess()) catch()		
		--try(postProcess()) catch(messageBox "Please press \"Setup Scene\" for add default render elements!" title: "Warning!")
		postProcess()
	)
	
	on _rStep3 open do
	(			
	)
	
	on cbxDR changed v do
	(
		checkDR type: #set
	)
)

rollout _rPreset  "Save / Load Preset" category:5
(	
	button btnSavePreset "Save" across: 2
	button btnLoadPreset "Load"
	
	global presetList = #(
		"RENDER_QUALITY",
		"RENDER_SIZE_X",
		"RENDER_SIZE_Y",			
		"AO_SIZE",
		"LIGHT_HDR",
		"REFLECT_HDR",
		"LIGHT_HDR_ANGLE",
		"LIGHT_HDR_MULT",
		"REFLECT_HDR_ANGLE",
		"REFLECT_HDR_MULT",
		"REFLECT_HDR_ON"
	)
	
	fn savePresetSetting f o = 
	(
		v = getMaxVersion()
		q = useSettings o "str" "r"
		
		setIniSetting f v o q
	)
	
	fn loadPresetSetting f o = 
	(
		v = getMaxVersion()
		
		q = getIniSetting f v o
		useSettings o q "w"				
	)
	
	on btnSavePreset pressed do
	(
		f = getSaveFileName  caption: "Save Preset"  types:"*.mkprev|*.mkprev"
		
		if(f == undefined) do return false
				
		for s in presetList do savePresetSetting f s
	)
	
	on btnLoadPreset pressed do
	(
		f = getOpenFileName  caption: "Save Preset"  types:"*.mkprev|*.mkprev"
		if(f == undefined) do return false
				
		for s in presetList do loadPresetSetting f s
			
		rebuildScript()
	)
)


rollout _rAbout "About" category:6
(
	label lblName "" 
	label lblVer "" 
	
	label lblAuthor "" height: 30
	label lblCopy ""  height: 30
	
	on _rAbout open do
	(	
		setRenderQuality()
							
		rolloutToggle (not isSetupScene())	
		
		i = getScriptInfo (getThisScriptFilename())
			
		lblName.caption = i[1]
		lblAuthor.caption = i[2]
		lblVer.caption = i[3]
		lblCopy.caption = i[6]
	)
)

addRollout _rStep0 fFastRender rolledUp:false
addRollout _rStep1 fFastRender rolledUp:true
addRollout _rStep2 fFastRender rolledUp:true
addRollout _rStep3 fFastRender rolledUp:false
addRollout _rPreset fFastRender rolledUp:true
addRollout _rAbout fFastRender rolledUp:true 