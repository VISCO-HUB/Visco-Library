/*  
[INFO] 

NAME = Make Preview
VERSION = 1.0.2
AUTHOR = MastaMan
DEV = VISCO
HELP = 

[INSTALL]

MACRO=MakePreview
CAT=VISCO
TEXT=Make Preview
ICON=#("PhysX_Main", 25)

[1.0.0]

* First release =

[1.0.2]

+ Added: VRay support=

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
	 
fn getRender =
(
	r = renderers.current as string

	if matchpattern r pattern:"*Corona*" do return #Corona
	if matchpattern r pattern:"*V_Ray_Adv*" do return #VRay
	return #unknown
)


fn deleteDomes =
(
	l = for i in lights where classOf i == VrayLight and i.type == 1 collect i
				
	try(delete l)catch()
)

fn setRenderQuality =
(
	renderSceneDialog.close()
	
	r = getRender()
	q = useSettings "RENDER_QUALITY" "int" "r"
	
	renderSystem = renderers.current 
	
	s = case q of
	(
		1: 600
		2: 800
		default: 400
	)
					
	renderWidth = s
	renderHeight = s
		
	case r of 
	(				
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
			renderSystem.colorMapping_type = 6
			renderSystem.colorMapping_darkMult = 1.0
			renderSystem.colorMapping_brightMult = 1.0
			renderSystem.environment_gi_on = true
			renderSystem.environment_gi_color = black
					
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

	renderSceneDialog.commit()
	renderSceneDialog.update()	
)

fn renderChange =
(
	renderSystem = renderers.current 
	_rStep0.qualitySet()	
	setRenderQuality()
)

rollout _rStep0  "Basic Setup"
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
		
	fn qualitySet =
	(
		s = useSettings "RENDER_QUALITY" "int" "r"
		sldQuality.value = s					
	)
	
	on _rStep0	open do
	(						
		callbacks.removeScripts id:#renderChange
		callbacks.removeScripts id:#postNew
		callbacks.removeScripts id:#postReset
		
		callbacks.addScript #postRendererChange "renderChange()" id:#renderChange
		callbacks.addScript #systemPostNew "renderChange()" id:#postNew
		callbacks.addScript #systemPostReset "renderChange()" id:#postReset
				
		qualitySet()
	)
	
	on _rStep0 close do
	(
		callbacks.removeScripts id:#renderChange
		callbacks.removeScripts id:#postNew
		callbacks.removeScripts id:#postReset
	)
		
	on btnSetupScene pressed do
	(
		r = getRender()
				
		if(r == #unknown) do return messageBox "Please assign Corona or V-Ray Renderer!" title: "Warning!" 
		
		case r of
		(
			#VRay:  deleteDomes()			
		)
		
		_rStep1.SetupScene()
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

rollout _rStep1  "HDRI"
(
	group "Light"
	(		
		spinner spnLightHDR "Angle:" across: 2 align: #left range: [0, 360, 0] type:#integer scale: 1.0 
		spinner spnLightHDR_Mult "Mult.:" align: #right range: [0.0, 9999.0, 1.0]  type:#float scale: 0.1  
		label lblStart "0°" align: #left across: 2
		label lblStop "360°" align: #right
		slider sldLightHDR "" ticks: 36 range:[0,360,0] type: #integer
		button btnLightHDR "None"
	)
	
	group "Reflections"
	(
		checkbox cbxUseReflect "Use Reflect HDR"
		spinner spnReflectHDR "Angle:" across: 2 align: #left range: [0, 360, 0] type: #integer scale: 1.0 		
		spinner spnReflectHDR_Mult "Mult.:"align: #right range: [0.0, 9999.0, 1.0]  type:#float scale: 0.1  
		label lblStart2 "0°" align: #left across: 2
		label lblStop2 "360°" align: #right
		slider sldReflectHDR "" ticks: 36 range:[0,360,0] type: #integer
		button btnReflectHDR "None"
	)
	
	timer tmrRotateLightHDR "" active: false interval: 100
	timer tmrRotateReflectHDR "" active: false interval: 100
	
	fn enableReflections =
	(
		c = cbxUseReflect.checked
		spnReflectHDR.enabled = c
		spnReflectHDR_Mult.enabled = c
		lblStart2.enabled = c
		lblStop2.enabled = c
		sldReflectHDR.enabled = c
		btnReflectHDR.enabled = c
	)
		
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
			)
			#VRay:
			(
				append renderElements (VRayGlobalIllumination())
				append renderElements (VRayReflection())
				append renderElements (VRayRefraction())
				
				cm = VrayExtraTex() 				
				cm.texture = VRayDirt()
				cm.texture.radius = _rStep0.spnAOSize.value
				
				append renderElements cm
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
				renderSystem.environment_rr_on = true			
				renderSystem.environment_rr_map_on = true	
			)
		)
		
		
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
		enableReflections()
	)
	
	on _rStep1	open do
	(			
		r = getRender()
		
		if(r == #unknown) do 
		(
			closeRolloutFloater fFastRender
			return messageBox "Please assign Corona or V-Ray Renderer!" title: "Warning!"
		)
						
		renderSystem = renderers.current 
		
		loadHDR()
		enableReflections()
		
		s = useSettings "AO_SIZE" "int" "r"
		if(s != 0) do _rStep0.spnAOSize.value = s
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
)

rollout _rStep2  "Highlights"
(	
	listbox lbxLights height: 8
	button btnAdd "Add Light" across: 2
	button btnRemove "Remove Light"
	
	group "Light Settings"
	(
		spinner spnW "W:" range: [0, 99999999, (units.decodeValue "80.0cm")] fieldWidth: 60 type: #worldunits across: 2 align: #left
		spinner spnH "H:" range: [0, 99999999, (units.decodeValue "50.0cm")] fieldWidth: 60 type: #worldunits  align: #left
		spinner spnMult "Mult.:" range: [-100, 999999, 1.0] fieldWidth: 40 type: #float align: #left offset: [0, 10] across: 2
		spinner spnDist "Dist.:" range: [0, 99999999, (units.decodeValue "150.0cm")] fieldWidth: 60 type: #worldunits  align: #left offset: [0, 10] scale: 1.0
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
				spnW.value = l.width
				spnH.value = l.height
				spnMult.value = l.intensity
			)
			#VRay:
			(
				spnW.value = l.size0
				spnH.value = l.size1
				spnMult.value = l.multiplier
			)
		)
				
		d = getUserProp l "HiglightDistance" 
		if(d != undefined) do spnDist.value = d
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
			#Corona: l = CoronaLight targeted:off height: h width: w pos:[0, 0, 0] isSelected: off name: (uniqueName "HighLight_") visibleDirectly: off shape: 1
			#VRay: l = VRayLight targeted:off size0: h size1: w pos:[0, 0, 0] isSelected: off name: (uniqueName "HighLight_") invisible: on type: 0
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
		
	on btnAdd pressed do addLight()
	on btnRemove pressed do removeLight()
	
	on _rStep2 open do
	(
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
)

fn postProcess =
(
	global rPostProcess
	global rPostSettings
	
	try(destroyDialog rPostProcess) catch()
	
	global postRender = undefined
	
	rollout rPostProcess "Post Process"
	(
		button btnImage "Wait while pre-rendering..." enabled: false border: false width: 800 height: 800 pos: [0, 0]
	
		subrollout subRollout1 "Settings"
		
		timer tmrShowRender "" active: false
				
		fn preRender = renderMap outputMap size:[800, 800] filter: true scale: 1.0
		
		fn clearRender = 
		(
			r = bitmap 1 1 color: black 
			btnImage.images = #(r, r, 1,1,1,1,1)
		)
		
		fn showRender now: false = 
		(	
			if(now != true ) do clearRender()
			
			postRender = preRender()
			
			tmrShowRender.interval = 500
			if(now == true ) do tmrShowRender.interval = 1
			
			tmrShowRender.active = true
		)
				
		fn saveImage =
		(
			f = getSaveFileName types: "JPG|*.jpg|PNG|*.png"
			if(f == undefined) do return false
			
			r = preRender()
			
			r.filename = f
			save r gamma: 2.2
			close r
		)
		
		on rPostProcess open do
		(				
			showRender()
		)
		
		on tmrShowRender tick do
		(
			if(postRender == undefined) do r = bitmap 1 1 color: black 
			
			b = bitmap 800 800 color: black 
			btnImage.images = #(postRender, b, 1,1,1,1,1)
			
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
	
		
	for i in 1 to outputMap.mapList.count  where outputMap.mapList[i] != undefined do 
	(	
		n = outputMap.mapList[i].name
		b = outputMap.blendMode[i]
		o = outputMap.opacity[i] as string 
		s = i as string
		en = if(i == 1) then "false" else "true"
		/* -- Save Settings
		
			blendMode = _rStep0.useSettings ("BLENDMODE" + s) "string" "r"
			if(blendMode != "") do b = blendMode as integer - 1
			
			opacityMode = _rStep0.useSettings ("OPACITY" + s) "string" "r"
			if(opacityMode != "") do o = opacityMode
			
			enableMode = _rStep0.useSettings ("EFFECT" + s) "string" "r"
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
	
	for i in 1 to outputMap.mapList.count  where outputMap.mapList[i] != undefined  do 
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
	
	ex += "button btnSave \"Save Image\" offset: [0, 15]\n"
	ex += " on btnSave pressed do rPostProcess.saveImage()"
	ex += ")\n"
	
	execute(ex)
	
	rPostProcess.subRollout1.pos = [800, 0]
	rPostProcess.subRollout1.width = 250
	rPostProcess.subRollout1.height = rPostProcess.height
	
	AddSubRollout rPostProcess.subRollout1 rPostSettings		
)

rollout _rStep3  "Render / Post Process"
(	
	

	group "Render"
	(
		--button btnStartInteractive "Start Interactive"
		button btnStartRender "Start Render" height: 30 width: 215		
	)
	group "Post Process"
	(
		button btnOpenEditor "Open Editor" height: 30 width: 215
	)
	
	timer tmrStartRender active: false interval: 100
	
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
		b = vrayVFBGetChannelBitmap 1
		
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
		
		/*
		-- CA / Lighten
		l = 7
		outputMap.mapList[l] = ColorCorrection()
		outputMap.mapList[l].map = copy main
		outputMap.mapList[l].map.coords.U_Offset = 0.0015		
		outputMap.mapList[l].lightnessMode = 1
		outputMap.mapList[l].enableR = on		
		outputMap.mapList[l].enableG = on		
		outputMap.mapList[l].enableB = on		
		outputMap.mapList[l].gainG = 0		
		outputMap.mapList[l].gainB = 0		
		outputMap.blendMode[l] = 8
		outputMap.opacity[l] = 40
		
		l = 8
		outputMap.mapList[l] = ColorCorrection()
		outputMap.mapList[l].map = copy main
		outputMap.mapList[l].map.coords.U_Offset = -0.0015			
		outputMap.mapList[l].lightnessMode = 1
		outputMap.mapList[l].enableR = on		
		outputMap.mapList[l].enableG = on		
		outputMap.mapList[l].enableB = on		
		outputMap.mapList[l].gainR = 0			
		outputMap.blendMode[l] = 8
		outputMap.opacity[l] = 40
		*/

		--outRender = renderMap outputMap size:[640,640] filter: true scale: 1.0
		--display outRender
						
		/*rollout rFinalResult  "Post Process" 
		(
			bitmap btmOutRender width: 640 height: 640 offset: [0, 35]
			timer tmrUpdateOutRender "" interval: 3000 
			
			on rFinalResult open do
			(
				
			)
			
			on rFinalResult close do
			(
				tmrUpdateOutRender.active = false
			)
				
			
			on tmrUpdateOutRender tick do
			(			
				
			)			
		)
		
		createDialog rFinalResult 640 (640 + 35)*/
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
		r = getRender()
	)
)


rollout _rAbout "About" 
(
	label lblName "" 
	label lblVer "" 
	
	label lblAuthor "" height: 30
	label lblCopy ""  height: 30
	
	on _rAbout open do
	(		
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
addRollout _rAbout fFastRender rolledUp:true 